<?php
// Ручное создание PHPMailer файлов
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Ручное создание PHPMailer</h1>";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📋 Инструкция:</h3>";
echo "<p>Если автоматическая установка не работает, вы можете создать PHPMailer вручную.</p>";
echo "<ol>";
echo "<li>Создайте директорию <code>vendor/phpmailer/phpmailer/src</code></li>";
echo "<li>Создайте файлы PHPMailer, Exception и SMTP</li>";
echo "<li>Создайте файл autoload.php</li>";
echo "</ol>";
echo "</div>";

// Показываем содержимое файлов для копирования
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Содержимое файлов:</h3>";

echo "<h4>1. vendor/autoload.php:</h4>";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'><?php
// Простой автозагрузчик для PHPMailer
spl_autoload_register(function (\$class) {
    \$prefix = \"PHPMailer\\\\PHPMailer\\\";
    \$base_dir = __DIR__ . \"/phpmailer/phpmailer/src/\";
    
    if (strpos(\$class, \$prefix) === 0) {
        \$relative_class = substr(\$class, strlen(\$prefix));
        \$file = \$base_dir . str_replace(\"\\\\\", \"/\", \$relative_class) . \".php\";
        
        if (file_exists(\$file)) {
            require \$file;
        }
    }
});
?></textarea>";

echo "<h4>2. vendor/phpmailer/phpmailer/src/Exception.php:</h4>";
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'><?php
namespace PHPMailer\\PHPMailer;

class Exception extends \\Exception {
    public function errorMessage() {
        return \$this->getMessage();
    }
}
?></textarea>";

echo "<h4>3. vendor/phpmailer/phpmailer/src/PHPMailer.php:</h4>";
echo "<p>(Этот файл слишком большой, но вы можете скопировать его из <a href='https://github.com/PHPMailer/PHPMailer' target='_blank'>официального репозитория</a>)</p>";

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>💡 Альтернатива:</h3>";
echo "<p>Поскольку SMTP уже работает (вы получили 2 тестовых письма), вы можете просто исправить обработчик формы:</p>";
echo "<ol>";
echo "<li>Откройте файл <code>assets/js/script.js</code></li>";
echo "<li>Найдите строку 65: <code>fetch('send_message_fallback.php', {</code></li>";
echo "<li>Замените на: <code>fetch('send_message_smtp_final.php', {</code></li>";
echo "<li>Сохраните файл</li>";
echo "</ol>";
echo "<p>Это позволит использовать SMTP без PHPMailer!</p>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
echo "<a href='QUICK_FIX.php' style='display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🚀 Быстрое исправление</a>";
echo "</div>";
?>