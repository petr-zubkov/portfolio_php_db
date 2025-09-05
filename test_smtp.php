<?php
// Тест SMTP соединения
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Тест SMTP соединения</h1>";

// Подключаем конфигурацию
require_once 'config.php';

// Проверяем настройки SMTP
echo "<div style='background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h3>Текущие настройки SMTP:</h3>";
echo "<strong>Host:</strong> " . SMTP_HOST . "<br>";
echo "<strong>Port:</strong> " . SMTP_PORT . "<br>";
echo "<strong>Username:</strong> " . SMTP_USERNAME . "<br>";
echo "<strong>Password:</strong> " . (SMTP_PASSWORD === 'your_password_here' ? '❌ Не настроен' : '✅ Настроен') . "<br>";
echo "<strong>From Email:</strong> " . SMTP_FROM_EMAIL . "<br>";
echo "<strong>To Email:</strong> " . SMTP_TO_EMAIL . "<br>";
echo "</div>";

// Проверяем, установлен ли PHPMailer
if (!file_exists('vendor/autoload.php')) {
    echo "<div style='color: red; font-weight: bold;'>❌ PHPMailer не установлен</div>";
    echo "<p><a href='install_phpmailer_working.php'>Установить PHPMailer</a></p>";
    exit;
}

require_once 'vendor/autoload.php';

if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    echo "<div style='color: red; font-weight: bold;'>❌ Класс PHPMailer не найден</div>";
    exit;
}

echo "<div style='color: green;'>✅ PHPMailer доступен</div>";

// Проверяем, настроен ли пароль
if (SMTP_PASSWORD === 'your_password_here') {
    echo "<div style='color: orange; font-weight: bold;'>⚠️ Пароль SMTP не настроен</div>";
    echo "<p>Пожалуйста, настройте пароль в config.php</p>";
    exit;
}

// Тестируем SMTP соединение
echo "<h3>Тест SMTP соединения...</h3>";

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
    
    // Тестовое письмо
    $mail->Subject = 'Тест SMTP с сайта zubkov.space';
    $mail->Body = "Это тестовое сообщение для проверки работы SMTP.\n\n" .
                 "Время: " . date('Y-m-d H:i:s') . "\n" .
                 "Сервер: " . $_SERVER['HTTP_HOST'] . "\n";
    
    // Отправляем письмо
    $mail->send();
    
    echo "<div style='color: green; font-weight: bold; font-size: 18px;'>✅ SMTP работает!</div>";
    echo "<p>Тестовое письмо успешно отправлено на " . SMTP_TO_EMAIL . "</p>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold;'>❌ Ошибка SMTP:</div>";
    echo "<div style='background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>" . $e->getMessage() . "</strong>";
    echo "</div>";
    
    echo "<h3>Возможные решения:</h3>";
    echo "<ul>";
    echo "<li>Проверьте правильность пароля от почты</li>";
    echo "<li>Убедитесь, что SMTP включен в настройках почты</li>";
    echo "<li>Попробуйте использовать порт 587 вместо 465</li>";
    echo "<li>Для Mail.ru может потребоваться создать пароль для приложений</li>";
    echo "</ul>";
}

// Тестируем альтернативные настройки
echo "<h3>Тест альтернативных настроек:</h3>";

$alt_configs = [
    [
        'host' => 'smtp.mail.ru',
        'port' => 587,
        'secure' => 'tls'
    ],
    [
        'host' => 'smtp.mail.ru',
        'port' => 465,
        'secure' => 'ssl'
    ]
];

foreach ($alt_configs as $config) {
    echo "<div style='margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;'>";
    echo "<strong>Тест: " . $config['host'] . ":" . $config['port'] . " (" . $config['secure'] . ")</strong><br>";
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->Port = $config['port'];
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        
        if ($config['secure'] === 'tls') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        }
        
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(SMTP_TO_EMAIL, SMTP_FROM_NAME);
        $mail->Subject = 'Тест ' . $config['host'] . ':' . $config['port'];
        $mail->Body = "Тест альтернативной конфигурации";
        
        $mail->send();
        echo "<span style='color: green;'>✅ Успех</span>";
        
    } catch (Exception $e) {
        echo "<span style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</span>";
    }
    
    echo "</div>";
}

echo "<div style='margin: 20px 0;'>";
echo "<a href='send_message_smtp.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>📧 Использовать SMTP обработчик</a>";
echo "</div>";
?>