<?php
// Улучшенный обработчик с несколькими методами отправки
header('Content-Type: application/json; charset=utf-8');

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен. Используйте POST.');
    }

    // Получаем данные
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Валидация
    if (empty($name)) throw new Exception('Введите имя');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Некорректный email');
    if (empty($message)) throw new Exception('Введите сообщение');

    // Защита
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Подключаем конфигурацию
    require_once 'config.php';

    // Сохраняем в базу данных
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'new')");
    $stmt->bind_param("sss", $name, $email, $message);
    $db_saved = $stmt->execute();

    $results = [];
    $debug_info = [];

    // Метод 1: SMTP (улучшенная версия)
    if (defined('SMTP_PASSWORD') && SMTP_PASSWORD !== 'your_password_here') {
        $smtp_result = sendViaSMTPImproved($name, $email, $message);
        $results['smtp'] = $smtp_result;
        $debug_info['smtp_details'] = $smtp_result['details'] ?? 'No details';
    } else {
        $results['smtp'] = false;
        $results['smtp_reason'] = 'Пароль не настроен';
    }

    // Метод 2: Стандартная mail() с улучшенными заголовками
    $mail_result = sendViaMailImproved($name, $email, $message);
    $results['mail'] = $mail_result;

    // Метод 3: Отправка на дополнительный email
    if (defined('SMTP_USERNAME') && SMTP_USERNAME !== SMTP_TO_EMAIL) {
        $mail2_result = sendViaMailImproved($name, $email, $message, SMTP_USERNAME);
        $results['secondary_email'] = $mail2_result;
    }

    // Метод 4: Логирование в файл (резервный метод)
    $log_result = logToFile($name, $email, $message);
    $results['file_log'] = $log_result;

    // Проверяем результаты
    $any_success = in_array(true, $results);

    if ($any_success) {
        $successful_methods = array_keys(array_filter($results));
        $method = implode(', ', $successful_methods);
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'methods_used' => $method,
                'results' => $results,
                'db_saved' => $db_saved,
                'debug_info' => $debug_info
            ]
        ]);
    } else {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Сообщение сохранено, но возникли проблемы с отправкой. Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'results' => $results,
                'db_saved' => $db_saved,
                'all_methods_failed' => true
            ]
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

exit;

// Улучшенная функция отправки через SMTP
function sendViaSMTPImproved($name, $email, $message) {
    try {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;
        $from_name = SMTP_FROM_NAME;

        $details = [];

        // Создаем контекст для SSL
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);

        $socket = @stream_socket_client(
            $host . ":" . $port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $details['connection'] = "Failed: $errstr";
            return ['success' => false, 'details' => $details];
        }

        $details['connection'] = "Success";

        // Читаем приветствие
        $greeting = fgets($socket, 515);
        $details['greeting'] = trim($greeting);

        // EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $ehlo_response = fgets($socket, 515);
        $details['ehlo'] = trim($ehlo_response);

        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $auth_response = fgets($socket, 515);
        $details['auth_start'] = trim($auth_response);

        if (substr($auth_response, 0, 3) !== "334") {
            fclose($socket);
            $details['auth_error'] = "Server not ready for username";
            return ['success' => false, 'details' => $details];
        }

        // Username
        fwrite($socket, base64_encode($username) . "\r\n");
        $user_response = fgets($socket, 515);
        $details['username'] = trim($user_response);

        if (substr($user_response, 0, 3) !== "334") {
            fclose($socket);
            $details['username_error'] = "Server not ready for password";
            return ['success' => false, 'details' => $details];
        }

        // Password
        fwrite($socket, base64_encode($password) . "\r\n");
        $pass_response = fgets($socket, 515);
        $details['password'] = trim($pass_response);

        if (substr($pass_response, 0, 3) !== "235") {
            fclose($socket);
            $details['auth_failed'] = "Authentication failed";
            return ['success' => false, 'details' => $details];
        }

        $details['authentication'] = "Success";

        // MAIL FROM
        fwrite($socket, "MAIL FROM:<" . $from_email . ">\r\n");
        $mail_response = fgets($socket, 515);
        $details['mail_from'] = trim($mail_response);

        // RCPT TO
        fwrite($socket, "RCPT TO:<" . $to_email . ">\r\n");
        $rcpt_response = fgets($socket, 515);
        $details['rcpt_to'] = trim($rcpt_response);

        // DATA
        fwrite($socket, "DATA\r\n");
        $data_response = fgets($socket, 515);
        $details['data'] = trim($data_response);

        // Формируем письмо
        $email_data = "From: " . $from_name . " <" . $from_email . ">\r\n";
        $email_data .= "To: " . $to_email . "\r\n";
        $email_data .= "Reply-To: " . $name . " <" . $email . ">\r\n";
        $email_data .= "Subject: =?UTF-8?B?" . base64_encode('Новое сообщение с сайта zubkov.space') . "?=\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "Content-Transfer-Encoding: base64\r\n";
        $email_data .= "\r\n";
        $email_data .= chunk_split(base64_encode(
            "Новое сообщение с сайта zubkov.space\r\n\r\n" .
            "Имя: " . $name . "\r\n" .
            "Email: " . $email . "\r\n" .
            "Дата: " . date('d.m.Y H:i:s') . "\r\n" .
            "IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n\r\n" .
            "Сообщение:\r\n" . $message . "\r\n\r\n" .
            "Отправлено с сайта: https://zubkov.space"
        ));
        $email_data .= "\r\n.\r\n";

        fwrite($socket, $email_data);
        $final_response = fgets($socket, 515);
        $details['final_response'] = trim($final_response);

        // QUIT
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        $success = substr($final_response, 0, 3) === "250";
        $details['success'] = $success;

        return ['success' => $success, 'details' => $details];

    } catch (Exception $e) {
        return ['success' => false, 'details' => ['exception' => $e->getMessage()]];
    }
}

// Улучшенная функция отправки через mail()
function sendViaMailImproved($name, $email, $message, $to_email = null) {
    if ($to_email === null) {
        $to_email = SMTP_TO_EMAIL;
    }

    $subject = 'Новое сообщение с сайта zubkov.space';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Return-Path: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: zubkov.space Form Mailer\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Originating-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
    $headers .= "X-Sender: " . SMTP_FROM_EMAIL . "\r\n";
    
    $body = "Новое сообщение с сайта zubkov.space\r\n\r\n";
    $body .= "========================================\r\n";
    $body .= "Имя: " . $name . "\r\n";
    $body .= "Email: " . $email . "\r\n";
    $body .= "Дата: " . date('d.m.Y H:i') . "\r\n";
    $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
    $body .= "========================================\r\n\r\n";
    $body .= "Сообщение:\r\n" . $message . "\r\n\r\n";
    $body .= "========================================\r\n";
    $body .= "Отправлено с сайта: https://zubkov.space\r\n";

    return mail($to_email, $subject, $body, $headers);
}

// Функция логирования в файл (резервный метод)
function logToFile($name, $email, $message) {
    $log_file = __DIR__ . '/messages_log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - Name: $name, Email: $email, Message: " . substr($message, 0, 100) . "...\n";
    
    return file_put_contents($log_file, $log_entry, FILE_APPEND) !== false;
}
?>