<?php
// Упрощенный SMTP обработчик без внешних зависимостей
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

    // Метод 1: SMTP отправка (наша реализация)
    if (defined('SMTP_PASSWORD') && SMTP_PASSWORD !== 'your_password_here') {
        $smtp_sent = sendViaSMTP($name, $email, $message);
        $results['smtp'] = $smtp_sent;
    } else {
        $results['smtp'] = false;
        $results['smtp_reason'] = 'Пароль не настроен';
    }

    // Метод 2: Стандартная mail() с улучшенными заголовками
    $mail_sent = sendViaMail($name, $email, $message);
    $results['mail'] = $mail_sent;

    // Метод 3: Отправка на дополнительный email
    if (defined('SMTP_USERNAME') && SMTP_USERNAME !== SMTP_TO_EMAIL) {
        $mail2_sent = sendViaMail($name, $email, $message, SMTP_USERNAME);
        $results['secondary_email'] = $mail2_sent;
    }

    // Проверяем результаты
    $any_success = $results['smtp'] || $results['mail'] || ($results['secondary_email'] ?? false);

    if ($any_success) {
        $method = $results['smtp'] ? 'SMTP' : ($results['mail'] ? 'mail()' : 'secondary_email');
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method_used' => $method,
                'results' => $results,
                'db_saved' => $db_saved
            ]
        ]);
    } else {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Сообщение сохранено, но возникли проблемы с отправкой. Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'results' => $results,
                'db_saved' => $db_saved
            ]
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage()
        ]
    ]);
}

exit;

// Функция отправки через нашу реализацию SMTP
function sendViaSMTP($name, $email, $message) {
    try {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;
        $from_name = SMTP_FROM_NAME;

        // Создаем сокетное соединение
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
            return false;
        }

        // Читаем приветствие сервера
        fgets($socket, 515);

        // EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        fgets($socket, 515);

        // Аутентификация
        fwrite($socket, "AUTH LOGIN\r\n");
        fgets($socket, 515);
        fwrite($socket, base64_encode($username) . "\r\n");
        fgets($socket, 515);
        fwrite($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 515);

        if (substr($response, 0, 3) !== "235") {
            fclose($socket);
            return false;
        }

        // Отправитель
        fwrite($socket, "MAIL FROM:<" . $from_email . ">\r\n");
        fgets($socket, 515);

        // Получатель
        fwrite($socket, "RCPT TO:<" . $to_email . ">\r\n");
        fgets($socket, 515);

        // Данные письма
        fwrite($socket, "DATA\r\n");
        fgets($socket, 515);

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
            "Дата: " . date('d.m.Y H:i') . "\r\n" .
            "IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n\r\n" .
            "Сообщение:\r\n" . $message . "\r\n\r\n" .
            "Отправлено с сайта: https://zubkov.space"
        ));
        $email_data .= "\r\n.\r\n";

        fwrite($socket, $email_data);
        $response = fgets($socket, 515);

        // Завершение сессии
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return substr($response, 0, 3) === "250";

    } catch (Exception $e) {
        return false;
    }
}

// Функция отправки через mail()
function sendViaMail($name, $email, $message, $to_email = null) {
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
?>