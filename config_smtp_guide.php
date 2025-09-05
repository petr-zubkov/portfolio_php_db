<?php
// Инструкция по настройке SMTP
header('Content-Type: text/html; charset=utf-8');

echo "<h1>📧 Настройка SMTP для надежной отправки почты</h1>";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h3>⚠️ Важно: Функция mail() работает, но письма не доходят</h3>";
echo "<p>Это распространенная проблема на хостингах. Решение - использовать SMTP с реальными учетными данными почты.</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<h3>✅ Преимущества SMTP:</h3>";
echo "<ul>";
echo "<li>Надежная доставка писем</li>";
echo "<li>Меньше попаданий в спам</li>";
echo "<li>Отслеживание доставки</li>";
echo "<li>Поддержка HTML-писем</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🔧 Шаг 1: Настройте Mail.ru</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>Включите SMTP в настройках Mail.ru:</h4>";
echo "<ol>";
echo "<li>Зайдите в вашу почту на mail.ru</li>";
echo "<li>Перейдите в Настройки → Все настройки</li>";
echo "<li>Выберите раздел \"Почтовые программы\"</li>";
echo "<li>Включите \"С сервера imap.mail.ru через протокол IMAP\"</li>";
echo "<li>Включите \"На сервер smtp.mail.ru через протокол SMTP\"</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>Создайте пароль для приложений (если нужно):</h4>";
echo "<ol>";
echo "<li>Перейдите в Настройки → Безопасность</li>";
echo "<li>Найдите раздел \"Пароли для внешних приложений\"</li>";
echo "<li>Создайте новый пароль для SMTP</li>";
echo "<li>Используйте этот пароль вместо основного</li>";
echo "</ol>";
echo "</div>";

echo "<h2>📝 Шаг 2: Обновите config.php</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>Замените текущие настройки:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto;'>
// Настройки почты
define('SMTP_HOST', 'smtp.mail.ru');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'petr-zubkov@mail.ru');
define('SMTP_PASSWORD', 'ВАШ_РЕАЛЬНЫЙ_ПАРОЛЬ'); // Замените на ваш пароль
define('SMTP_FROM_EMAIL', 'petr-zubkov@mail.ru');
define('SMTP_FROM_NAME', 'Пётр Зубков');
define('SMTP_TO_EMAIL', 'petr-zubkov@mail.ru');
</pre>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; margin: 15px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>⚠️ Важно:</h4>";
echo "<ul>";
echo "<li>Замените <code>ВАШ_РЕАЛЬНЫЙ_ПАРОЛЬ</code> на ваш пароль от mail.ru</li>";
echo "<li>Если основной пароль не работает, используйте пароль для приложений</li>";
echo "<li>Порт 465 использует SSL, порт 587 использует TLS</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🧪 Шаг 3: Протестируйте SMTP</h2>";

echo "<div style='margin: 20px 0;'>";
echo "<a href='install_phpmailer_working.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>1. Установить PHPMailer</a>";
echo "<a href='test_smtp.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>2. Тест SMTP</a>";
echo "</div>";

echo "<h2>📧 Шаг 4: Используйте SMTP обработчик</h2>";

echo "<p>После успешного теста SMTP, обновите JavaScript для использования SMTP-обработчика:</p>";

echo "<div style='background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>В файле assets/js/script.js замените:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto;'>
// Было:
fetch('send_message_with_logging.php', {

// Стало:
fetch('send_message_smtp.php', {
</pre>";
echo "</div>";

echo "<h2>🔄 Альтернативные варианты</h2>";

echo "<div style='background: #e2e3e5; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h4>Если SMTP не работает:</h4>";
echo "<ul>";
echo "<li><strong>Telegram-уведомления:</strong> Самый надежный способ</li>";
echo "<li><strong>Внешние сервисы:</strong> SendGrid, Mailgun</li>";
echo "<li><strong>Google Forms:</strong> Перенаправление формы на Google Forms</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📱 Telegram-уведомления (рекомендуется)</h2>";

echo "<div style='background: #d1ecf1; padding: 15px; margin: 15px 0; border: 1px solid #bee5eb; border-radius: 5px;'>";
echo "<h4>Как настроить:</h4>";
echo "<ol>";
echo "<li>Найдите в Telegram <strong>@BotFather</strong></li>";
echo "<li>Создайте нового бота командой <code>/newbot</code></li>";
echo "<li>Получите токен бота</li>";
echo "<li>Найдите <strong>@userinfobot</strong> и узнайте свой chat_id</li>";
echo "<li>Вставьте данные в <code>send_message_external.php</code></li>";
echo "</ol>";
echo "</div>";

echo "<div style='margin: 20px 0; text-align: center;'>";
echo "<p><strong>Начните с установки PHPMailer и теста SMTP!</strong></p>";
echo "</div>";
?>