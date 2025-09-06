<?php
// Отключаем вывод ошибок в браузер
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Устанавливаем заголовок JSON
header('Content-Type: application/json');

try {
    // Проверяем существование config.php
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("Файл конфигурации не найден по пути: $configPath");
    }
    
    // Подключаем конфиг
    require_once $configPath;
    
    // Проверяем определение констант после подключения
    $requiredConstants = [
        'DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME',
        'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS',
        'ADMIN_EMAIL'
    ];
    
    $undefinedConstants = [];
    foreach ($requiredConstants as $const) {
        if (!defined($const)) {
            $undefinedConstants[] = $const;
        }
    }
    
    if (!empty($undefinedConstants)) {
        throw new Exception("Не определены константы: " . implode(', ', $undefinedConstants));
    }
    
    // Функция для отправки почты через SMTP
    function sendFixedSMTP($to, $subject, $message, $fromName, $fromEmail) {
        $smtpHost = SMTP_HOST;
        $smtpPort = SMTP_PORT;
        $smtpUsername = SMTP_USER;
        $smtpPassword = SMTP_PASS;
        
        // Формируем заголовки письма
        // Важно: From должен быть адресом, под которым мы авторизовались
        $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
        $headers .= "Reply-To: $fromName <$fromEmail>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Создаем SSL-контекст
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        // Подключаемся к SMTP-серверу
        $socket = @stream_socket_client(
            "ssl://$smtpHost:$smtpPort",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            throw new Exception("Ошибка подключения: $errstr ($errno)");
        }
        
        // Получаем приветствие сервера
        $greeting = fgets($socket, 515);
        if (substr($greeting, 0, 3) != 220) {
            fclose($socket);
            throw new Exception("Ошибка приветствия: " . trim($greeting));
        }
        
        // Отправляем EHLO
        fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $ehloResponse = fgets($socket, 515);
        if (substr($ehloResponse, 0, 3) != 250) {
            fclose($socket);
            throw new Exception("Ошибка EHLO: " . trim($ehloResponse));
        }
        
        // Читаем multiline ответ EHLO
        while (substr(fgets($socket, 515), 0, 4) == '250-') {
            // Пропускаем дополнительные строки
        }
        
        // Авторизация
        fputs($socket, "AUTH LOGIN\r\n");
        if (substr(fgets($socket, 515), 0, 3) != 334) {
            fclose($socket);
            throw new Exception("Ошибка запроса авторизации");
        }
        
        // Отправляем логин
        fputs($socket, base64_encode($smtpUsername) . "\r\n");
        if (substr(fgets($socket, 515), 0, 3) != 334) {
            fclose($socket);
            throw new Exception("Ошибка авторизации логина");
        }
        
        // Отправляем пароль
        fputs($socket, base64_encode($smtpPassword) . "\r\n");
        $authPassResponse = fgets($socket, 515);
        if (substr($authPassResponse, 0, 3) != 235) {
            fclose($socket);
            throw new Exception("Ошибка авторизации пароля: " . trim($authPassResponse));
        }
        
        // ВАЖНО: В команде MAIL FROM используем адрес, под которым авторизовались
        fputs($socket, "MAIL FROM: <" . SMTP_FROM . ">\r\n");
        $fromResponse = fgets($socket, 515);
        if (substr($fromResponse, 0, 3) != 250) {
            fclose($socket);
            throw new Exception("Ошибка установки отправителя: " . trim($fromResponse));
        }
        
        // Устанавливаем получателя
        fputs($socket, "RCPT TO: <$to>\r\n");
        $toResponse = fgets($socket, 515);
        if (substr($toResponse, 0, 3) != 250) {
            fclose($socket);
            throw new Exception("Ошибка установки получателя: " . trim($toResponse));
        }
        
        // Начинаем отправку данных
        fputs($socket, "DATA\r\n");
        if (substr(fgets($socket, 515), 0, 3) != 354) {
            fclose($socket);
            throw new Exception("Ошибка начала передачи данных");
        }
        
        // Формируем тело письма
        $emailData = "To: $to\r\n";
        $emailData .= "Subject: $subject\r\n";
        $emailData .= $headers;
        $emailData .= "\r\n";
        $emailData .= $message;
        $emailData .= "\r\n.\r\n";
        
        // Отправляем письмо
        fputs($socket, $emailData);
        $sendResponse = fgets($socket, 515);
        if (substr($sendResponse, 0, 3) != 250) {
            fclose($socket);
            throw new Exception("Ошибка отправки письма: " . trim($sendResponse));
        }
        
        // Завершаем сессию
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        return true;
    }
    
    // Обработка POST-запроса
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получаем и очищаем данные
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        // Валидация
        $errors = [];
        if (empty($name)) $errors[] = "Введите имя";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Введите корректный email";
        if (empty($message)) $errors[] = "Введите сообщение";
        
        if (!empty($errors)) {
            throw new Exception(implode('<br>', $errors));
        }
        
        // Подключение к БД
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            throw new Exception("Ошибка подключения к БД: " . $conn->connect_error);
        }
        
        // Сохранение в БД
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            $conn->close();
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $name, $email, $message);
        $dbSaved = $stmt->execute();
        $stmt->close();
        $conn->close();
        
        if (!$dbSaved) {
            throw new Exception("Ошибка сохранения в БД");
        }
        
        // Формируем письмо
        $subject = "Новое сообщение с сайта от $name";
        $emailMessage = "Получено новое сообщение:\n\n";
        $emailMessage .= "Имя: $name\n";
        $emailMessage .= "Email: $email\n";
        $emailMessage .= "Сообщение:\n$message\n\n";
        $emailMessage .= "Дата: " . date('d.m.Y H:i:s');
        
        // Отправляем письмо
        $smtpSent = sendFixedSMTP(
            ADMIN_EMAIL,
            $subject,
            $emailMessage,
            $name,
            $email
        );
        
        if (!$smtpSent) {
            throw new Exception("Ошибка отправки письма");
        }
        
        // Успешный ответ
        echo json_encode([
            'success' => true,
            'message' => "Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.",
            'debug' => [
                'method' => 'Fixed SMTP',
                'db_saved' => $dbSaved,
                'smtp_sent' => $smtpSent,
                'to_email' => ADMIN_EMAIL,
                'note' => 'Исправленная версия SMTP обработчика'
            ]
        ]);
        
    } else {
        throw new Exception('Метод не поддерживается');
    }
    
} catch (Exception $e) {
    // Логируем ошибку
    error_log("Ошибка в send_message_fixed_smtp.php: " . $e->getMessage());
    
    // Возвращаем ошибку в JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>