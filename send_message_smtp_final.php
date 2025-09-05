<?php
// Финальный SMTP обработчик с обработкой ошибок
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

    // Проверяем наличие PHPMailer
    if (!file_exists('vendor/autoload.php')) {
        throw new Exception('PHPMailer не установлен. <a href="install_phpmailer_working.php">Установить PHPMailer</a>');
    }

    require_once 'vendor/autoload.php';

    // Проверяем настройки SMTP
    if (!defined('SMTP_PASSWORD') || SMTP_PASSWORD === 'your_password_here') {
        throw new Exception('SMTP не настроен. Настройте пароль в config.php');
    }

    // Создаем экземпляр PHPMailer
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
    $mail->SMTPDebug = 0; // Отключаем отладку

    // Отправитель и получатель
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress(SMTP_TO_EMAIL, SMTP_FROM_NAME);
    $mail->addReplyTo($email, $name);

    // Тема и тело письма
    $mail->Subject = 'Новое сообщение с сайта zubkov.space';
    
    // HTML версия письма
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; border-radius: 5px; }
            .content { background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
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
                    <strong>👤 От:</strong> $name<br>
                    <strong>📧 Email:</strong> $email<br>
                    <strong>📅 Дата:</strong> " . date('d.m.Y H:i') . "
                </div>
                <div class='message'>
                    <strong>💬 Сообщение:</strong><br>
                    " . nl2br($message) . "
                </div>
            </div>
            <div class='footer'>
                Это сообщение было отправлено через контактную форму сайта zubkov.space<br>
                IP адрес: " . $_SERVER['REMOTE_ADDR'] . "
            </div>
        </div>
    </body>
    </html>";

    // Текстовая версия письма
    $mail->AltBody = "Новое сообщение с сайта zubkov.space\n\n" .
                     "Имя: $name\n" .
                     "Email: $email\n" .
                     "Дата: " . date('d.m.Y H:i') . "\n\n" .
                     "Сообщение:\n$message";

    $mail->isHTML(true);

    // Отправляем письмо
    $mail->send();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Ваше сообщение успешно отправлено через SMTP! Я свяжусь с вами в ближайшее время.',
        'debug' => [
            'method' => 'SMTP',
            'db_saved' => $db_saved,
            'to_email' => SMTP_TO_EMAIL,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при отправке: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'db_saved' => $db_saved ?? false,
            'method' => 'SMTP'
        ]
    ]);
}

exit;
?>