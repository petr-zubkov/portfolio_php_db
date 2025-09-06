<?php
// Прямой SMTP обработчик без PHPMailer
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

    // Прямая SMTP отправка (без PHPMailer)
    $smtp_sent = sendDirectSMTP($name, $email, $message);

    if ($smtp_sent) {
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method' => 'Direct SMTP',
                'db_saved' => $db_saved,
                'smtp_sent' => true,
                'to_email' => SMTP_TO_EMAIL
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
                'note' => 'SMTP отправка не удалась, но сообщение сохранено в базе данных'
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

function sendDirectSMTP($name, $email, $message) {
    try {
        require_once 'config.php';
        
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;
        
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
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        fgets($socket, 515);
        
        // Получатель
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        fgets($socket, 515);
        
        // Данные письма
        fwrite($socket, "DATA\r\n");
        fgets($socket, 515);
        
        // Формируем письмо
        $subject = 'Новое сообщение с сайта zubkov.space';
        $email_data = "From: $from_email\r\n";
        $email_data .= "To: $to_email\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "Reply-To: $email\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "Имя: $name\r\n";
        $email_data .= "Email: $email\r\n";
        $email_data .= "Дата: " . date('d.m.Y H:i') . "\r\n";
        $email_data .= "\r\n";
        $email_data .= "Сообщение:\r\n";
        $email_data .= "$message\r\n";
        $email_data .= "\r\n.\r\n";
        
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