?php
// Универсальный обработчик контактной формы
// Работает с PHPMailer или с встроенной функцией mail()

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

// Устанавливаем заголовки
header('Content-Type: application/json; charset=utf-8');

try {
    // Проверяем, что это POST запрос
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен');
    }

    // Получаем данные из формы
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Валидация данных
    if (empty($name)) {
        throw new Exception('Пожалуйста, введите ваше имя');
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Пожалуйста, введите корректный email');
    }

    if (empty($message)) {
        throw new Exception('Пожалуйста, введите ваше сообщение');
    }

    // Защита от XSS
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Подключаем конфигурацию
    require_once 'config.php';

    // Сохраняем сообщение в базу данных
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'new')");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if (!$stmt->execute()) {
        throw new Exception('Ошибка при сохранении сообщения в базу данных');
    }

    // Пробуем отправить через PHPMailer, если он установлен
    $phpmailer_available = false;
    if (file_exists('vendor/autoload.php')) {
        try {
            require_once 'vendor/autoload.php';
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                $phpmailer_available = true;
            }
        } catch (Exception $e) {
            // PHPMailer не доступен, будем использовать mail()
        }
    }

    if ($phpmailer_available) {
        // Отправляем через PHPMailer
        $mail_sent = sendWithPHPMailer($name, $email, $message);
    } else {
        // Отправляем через функцию mail()
        $mail_sent = sendWithMail($name, $email, $message);
    }

    if ($mail_sent) {
        // Очищаем буфер и отправляем успешный ответ
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.'
        ]);
    } else {
        throw new Exception('Ошибка при отправке письма. Пожалуйста, попробуйте позже.');
    }

} catch (Exception $e) {
    // Очищаем буфер и отправляем ошибку
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при отправке сообщения: ' . $e->getMessage()
    ]);
}

exit;

// Функция отправки через PHPMailer
function sendWithPHPMailer($name, $email, $message) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        // Отправитель и получатель
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(SMTP_TO_EMAIL, SMTP_FROM_NAME);
        $mail->addReplyTo($email, $name);

        // Тема письма
        $mail->Subject = 'Новое сообщение с сайта zubkov.space';

        // Тело письма
        $mailContent = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { background-color: #f9f9f9; padding: 20px; border-radius: 5px; }
                .info { background-color: #e8f4f8; padding: 15px; margin: 10px 0; border-radius: 5px; }
                .message { background-color: #fff; padding: 15px; border-left: 4px solid #3498db; margin: 10px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Новое сообщение с сайта</h2>
                </div>
                <div class='content'>
                    <div class='info'>
                        <strong>От:</strong> $name<br>
                        <strong>Email:</strong> $email<br>
                        <strong>Дата:</strong> " . date('d.m.Y H:i') . "
                    </div>
                    <div class='message'>
                        <strong>Сообщение:</strong><br>
                        " . nl2br($message) . "
                    </div>
                </div>
                <div class='footer'>
                    Это сообщение было отправлено через контактную форму сайта zubkov.space
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $mailContent;
        $mail->isHTML(true);

        // Отправляем письмо
        $mail->send();
        return true;

    } catch (Exception $e) {
        // Если PHPMailer не работает, пробуем через mail()
        return sendWithMail($name, $email, $message);
    }
}

// Функция отправки через mail()
function sendWithMail($name, $email, $message) {
    $to = defined('SMTP_TO_EMAIL') ? SMTP_TO_EMAIL : 'petr-zubkov@mail.ru';
    $subject = 'Новое сообщение с сайта zubkov.space';
    
    // Формируем заголовки
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: " . (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@zubkov.space') . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Формируем тело письма
    $body = "Новое сообщение с сайта zubkov.space\n\n";
    $body .= "========================================\n";
    $body .= "Имя: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Дата: " . date('d.m.Y H:i') . "\n";
    $body .= "========================================\n\n";
    $body .= "Сообщение:\n" . $message . "\n\n";
    $body .= "========================================\n";
    $body .= "Это сообщение было отправлено через контактную форму сайта zubkov.space\n";

    // Отправляем письмо
    return mail($to, $subject, $body, $headers);
}
?>