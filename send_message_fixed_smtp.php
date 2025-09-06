<?php
// Исправленный SMTP обработчик
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

    // Отправляем через SMTP с передачей настроек
    $smtp_sent = sendFixedSMTP($name, $email, $message, SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD, SMTP_FROM_EMAIL, SMTP_TO_EMAIL);

    if ($smtp_sent) {
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method' => 'Fixed SMTP',
                'db_saved' => $db_saved,
                'smtp_sent' => true,
                'to_email' => SMTP_TO_EMAIL,
                'note' => 'Исправленная версия SMTP обработчика'
            ]
        ]);
    } else {
        // Если SMTP не сработал, но сохранено в БД
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение получено и сохранено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method' => 'Database Backup',
                'db_saved' => $db_saved,
                'smtp_sent' => false,
                'note' => 'SMTP не сработал, но сообщение сохранено в базе данных'
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

// Исправленная функция отправки SMTP
function sendFixedSMTP($name, $email, $message, $host, $port, $username, $password, $from_email, $to_email) {
    try {
        // Создаем контекст для SSL
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);
        
        // Подключаемся к SMTP серверу
        $socket = @stream_socket_client(
            "ssl://$host:$port",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            return false;
        }
        
        // Устанавливаем таймаут
        stream_set_timeout($socket, 10);
        
        // Читаем приветствие сервера
        $response = fgets($socket, 515);
        if (!$response || substr($response, 0, 3) !== "220") {
            fclose($socket);
            return false;
        }
        
        // EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        
        // Читаем multiline ответ EHLO
        $ehlo_response = "";
        while (true) {
            $line = fgets($socket, 515);
            if (!$line) break;
            
            $ehlo_response .= $line;
            if (substr($line, 3, 1) === " ") break; // Последняя строка
        }
        
        // Проверяем поддержку AUTH
        if (strpos($ehlo_response, 'AUTH') === false) {
            fclose($socket);
            return false;
        }
        
        // Аутентификация LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "334") {
            fclose($socket);
            return false;
        }
        
        // Отправляем логин
        fwrite($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "334") {
            fclose($socket);
            return false;
        }
        
        // Отправляем пароль
        fwrite($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "235") {
            fclose($socket);
            return false;
        }
        
        // MAIL FROM
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "250") {
            fclose($socket);
            return false;
        }
        
        // RCPT TO
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "250") {
            fclose($socket);
            return false;
        }
        
        // DATA
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "354") {
            fclose($socket);
            return false;
        }
        
        // Формируем письмо
        $subject = 'Новое сообщение с сайта zubkov.space';
        $body = "Новое сообщение с сайта zubkov.space\n\n";
        $body .= "Имя: $name\n";
        $body .= "Email: $email\n";
        $body .= "Дата: " . date('d.m.Y H:i') . "\n";
        $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $body .= "Сообщение:\n$message";
        
        $email_data = "From: $from_email\r\n";
        $email_data .= "To: $to_email\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "Reply-To: $email\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "$body\r\n";
        $email_data .= ".\r\n";
        
        fwrite($socket, $email_data);
        $response = fgets($socket, 515);
        
        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return substr($response, 0, 3) === "250";
        
    } catch (Exception $e) {
        return false;
    }
}
?>