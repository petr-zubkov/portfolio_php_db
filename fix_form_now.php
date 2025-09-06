<?php
// Быстрое исправление формы
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🚀 Быстрое исправление формы</h1>";

echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>❌ Проблема найдена!</h2>";
echo "<p>Форма использует <code>send_message_fallback.php</code> который сохраняет сообщения, но не отправляет письма.</p>";
echo "<p>Нужно заменить на <code>send_message_direct_smtp.php</code> который использует рабочий SMTP.</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>✅ Решение:</h2>";
echo "<p>Замените одну строку в файле <code>assets/js/script.js</code></p>";
echo "</div>";

// Показываем текущий файл
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Текущий файл script.js:</h3>";

if (file_exists('assets/js/script.js')) {
    $content = file_get_contents('assets/js/script.js');
    
    // Находим строку с fetch
    if (preg_match("/(fetch\('[^']+'\s*,\s*\{)/", $content, $matches)) {
        $current_line = $matches[0];
        echo "<div style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin: 15px 0;'>";
        echo "<strong>Текущая строка:</strong><br>";
        echo "<code style='background: #f8f9fa; padding: 10px; border-radius: 3px; display: block; margin: 10px 0;'>" . htmlspecialchars($current_line) . "</code>";
        
        if (strpos($current_line, 'send_message_fallback.php') !== false) {
            echo "<p style='color: orange;'>⚠️ Нужно заменить на send_message_direct_smtp.php</p>";
            
            $new_line = str_replace('send_message_fallback.php', 'send_message_direct_smtp.php', $current_line);
            echo "<p><strong>Новая строка:</strong></p>";
            echo "<code style='background: #d4edda; padding: 10px; border-radius: 3px; display: block; margin: 10px 0;'>" . htmlspecialchars($new_line) . "</code>";
        } else {
            echo "<p style='color: green;'>✅ Уже исправлено!</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ Не удалось прочитать файл script.js</p>";
}

echo "</div>";

// Кнопка для автоматического исправления
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🔧 Автоматическое исправление:</h3>";
echo "<form method='post'>";
echo "<input type='hidden' name='fix_script' value='1'>";
echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>🚀 Исправить автоматически</button>";
echo "</form>";
echo "</div>";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_script'])) {
    if (file_exists('assets/js/script.js')) {
        $content = file_get_contents('assets/js/script.js');
        $new_content = str_replace('send_message_fallback.php', 'send_message_direct_smtp.php', $content);
        
        if (file_put_contents('assets/js/script.js', $new_content)) {
            echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h2>✅ Успешно исправлено!</h2>";
            echo "<p>Файл script.js обновлен. Теперь форма использует send_message_direct_smtp.php</p>";
            echo "<p>Попробуйте отправить сообщение с формы на сайте - письма должны приходить!</p>";
            echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>🏠 Проверить форму на сайте</a>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h2>❌ Ошибка при сохранении файла</h2>";
            echo "<p>Проверьте права доступа к файлу assets/js/script.js</p>";
            echo "</div>";
        }
    }
}

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📋 Что произойдет после исправления:</h3>";
echo "<ul>";
echo "<li>Форма будет использовать прямой SMTP (как в тесте)</li>";
echo "<li>Письма будут приходить на petr-zubkov@mail.ru</li>";
echo "<li>Если SMTP не сработает, сообщение сохранится в базе данных</li>";
echo "<li>Вы получите уведомление об успешной отправке</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='check_saved_messages.php' style='display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>📋 Проверить сохраненные сообщения</a>";
echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
echo "</div>";
?>