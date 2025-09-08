<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include config file
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get and sanitize form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        // Validation
        $errors = [];
        if (empty($name)) $errors[] = "Введите имя";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Введите корректный email";
        if (empty($message)) $errors[] = "Введите сообщение";
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: index.php#contact');
            exit;
        }
        
        // Connect to database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            throw new Exception("Ошибка подключения к БД: " . $conn->connect_error);
        }
        
        // Save to database
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
        
        // SMTP Email Sending Function
        function sendSMTPMail($to, $subject, $message, $fromName, $fromEmail) {
            $smtpHost = SMTP_HOST;
            $smtpPort = SMTP_PORT;
            $smtpUsername = SMTP_USER;
            $smtpPassword = SMTP_PASS;
            
            // Create SSL context
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            // Connect to SMTP server
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
            
            // Get server greeting
            $greeting = fgets($socket, 515);
            if (substr($greeting, 0, 3) != 220) {
                fclose($socket);
                throw new Exception("Ошибка приветствия: " . trim($greeting));
            }
            
            // Send EHLO
            fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $ehloResponse = fgets($socket, 515);
            if (substr($ehloResponse, 0, 3) != 250) {
                fclose($socket);
                throw new Exception("Ошибка EHLO: " . trim($ehloResponse));
            }
            
            // Read multiline EHLO response
            while (substr(fgets($socket, 515), 0, 4) == '250-') {
                // Skip additional lines
            }
            
            // Authentication
            fputs($socket, "AUTH LOGIN\r\n");
            if (substr(fgets($socket, 515), 0, 3) != 334) {
                fclose($socket);
                throw new Exception("Ошибка запроса авторизации");
            }
            
            // Send username
            fputs($socket, base64_encode($smtpUsername) . "\r\n");
            if (substr(fgets($socket, 515), 0, 3) != 334) {
                fclose($socket);
                throw new Exception("Ошибка авторизации логина");
            }
            
            // Send password
            fputs($socket, base64_encode($smtpPassword) . "\r\n");
            $authPassResponse = fgets($socket, 515);
            if (substr($authPassResponse, 0, 3) != 235) {
                fclose($socket);
                throw new Exception("Ошибка авторизации пароля: " . trim($authPassResponse));
            }
            
            // Set sender (use the authenticated email address)
            fputs($socket, "MAIL FROM: <" . SMTP_FROM . ">\r\n");
            $fromResponse = fgets($socket, 515);
            if (substr($fromResponse, 0, 3) != 250) {
                fclose($socket);
                throw new Exception("Ошибка установки отправителя: " . trim($fromResponse));
            }
            
            // Set recipient
            fputs($socket, "RCPT TO: <$to>\r\n");
            $toResponse = fgets($socket, 515);
            if (substr($toResponse, 0, 3) != 250) {
                fclose($socket);
                throw new Exception("Ошибка установки получателя: " . trim($toResponse));
            }
            
            // Start data transmission
            fputs($socket, "DATA\r\n");
            if (substr(fgets($socket, 515), 0, 3) != 354) {
                fclose($socket);
                throw new Exception("Ошибка начала передачи данных");
            }
            
            // Format email headers
            $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">\r\n";
            $headers .= "Reply-To: $fromName <$fromEmail>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // Format email data
            $emailData = "To: $to\r\n";
            $emailData .= "Subject: $subject\r\n";
            $emailData .= $headers;
            $emailData .= "\r\n";
            $emailData .= $message;
            $emailData .= "\r\n.\r\n";
            
            // Send email
            fputs($socket, $emailData);
            $sendResponse = fgets($socket, 515);
            if (substr($sendResponse, 0, 3) != 250) {
                fclose($socket);
                throw new Exception("Ошибка отправки письма: " . trim($sendResponse));
            }
            
            // Close connection
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
        }
        
        // Prepare email content
        $subject = "Новое сообщение с сайта от $name";
        $emailMessage = "Получено новое сообщение:\n\n";
        $emailMessage .= "Имя: $name\n";
        $emailMessage .= "Email: $email\n";
        $emailMessage .= "Сообщение:\n$message\n\n";
        $emailMessage .= "Дата: " . date('d.m.Y H:i:s');
        
        // Send email using SMTP
        $smtpSent = sendSMTPMail(
            ADMIN_EMAIL,
            $subject,
            $emailMessage,
            $name,
            $email
        );
        
        if (!$smtpSent) {
            throw new Exception("Ошибка отправки письма");
        }
        
        // Success message
        $_SESSION['success'] = "Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.";
        header('Location: index.php#contact');
        exit;
        
    } catch (Exception $e) {
        // Log error
        error_log("Ошибка в send_message.php: " . $e->getMessage());
        
        // Show error to user
        $_SESSION['error'] = "Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже или свяжитесь со мной другим способом.";
        header('Location: index.php#contact');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>