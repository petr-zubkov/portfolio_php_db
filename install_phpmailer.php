<?php
// Установка PHPMailer через Composer
// Этот файл нужно запустить один раз для установки зависимостей

echo "<h2>Установка PHPMailer</h2>";

// Проверяем, есть ли composer.phar
if (!file_exists('composer.phar')) {
    echo "<p>Скачиваем Composer...</p>";
    copy('https://getcomposer.org/composer-stable.phar', 'composer.phar');
}

// Устанавливаем PHPMailer
echo "<p>Устанавливаем PHPMailer...</p>";
$output = shell_exec('php composer.phar require phpmailer/phpmailer');

echo "<pre>$output</pre>";

if (file_exists('vendor/autoload.php')) {
    echo "<div style='color: green;'><h3>✅ PHPMailer успешно установлен!</h3></div>";
    echo "<p>Теперь можно отправлять письма через SMTP.</p>";
} else {
    echo "<div style='color: red;'><h3>❌ Ошибка установки PHPMailer</h3></div>";
}
?>