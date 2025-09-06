<?php
// Проверка сохраненных сообщений
header('Content-Type: text/html; charset=utf-8');

echo "<h1>📋 Проверка сохраненных сообщений</h1>";

require_once 'config.php';

// Проверяем базу данных
echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>💾 Сообщения в базе данных:</h2>";

try {
    $result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 10");
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    
    if (count($messages) > 0) {
        echo "<div style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;'>";
        foreach ($messages as $msg) {
            echo "<div style='border-bottom: 1px solid #eee; padding: 10px 0; margin-bottom: 10px;'>";
            echo "<div><strong>📅 " . $msg['created_at'] . "</strong> | <span style='color: " . ($msg['status'] === 'new' ? 'orange' : 'green') . ";'>📊 " . $msg['status'] . "</span></div>";
            echo "<div><strong>👤 " . htmlspecialchars($msg['name']) . "</strong> | 📧 " . htmlspecialchars($msg['email']) . "</div>";
            echo "<div style='margin-top: 5px; background: #f8f9fa; padding: 10px; border-radius: 3px;'>" . nl2br(htmlspecialchars($msg['message'])) . "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>Сообщений в базе данных нет</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка при чтении базы данных: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Проверяем файлы бэкапа
echo "<div style='background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>📄 Файлы бэкапа:</h2>";

$backup_files = glob('message_backup_*.txt');
if (count($backup_files) > 0) {
    foreach ($backup_files as $file) {
        echo "<div style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 15px;'>";
        echo "<h4>📁 $file</h4>";
        
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (!empty($content)) {
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 3px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($content) . "</pre>";
            } else {
                echo "<p>Файл пустой</p>";
            }
        } else {
            echo "<p style='color: red;'>Файл не существует</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p>Файлов бэкапа не найдено</p>";
}

echo "</div>";

// Проверяем логи
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>📝 Логи:</h2>";

if (file_exists('messages_log.txt')) {
    $log_content = file_get_contents('messages_log.txt');
    if (!empty($log_content)) {
        echo "<pre style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($log_content) . "</pre>";
    } else {
        echo "<p>Логи пустые</p>";
    }
} else {
    echo "<p>Файл логов не найден</p>";
}

echo "</div>";

// Инструкции
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🔧 Что делать дальше:</h3>";
echo "<ol>";
echo "<li><strong>Проверьте сообщения выше</strong> - ваши тестовые сообщения должны быть сохранены</li>";
echo "<li><strong>Исправьте обработчик формы:</strong></li>";
echo "<ul>";
echo "<li>Откройте <code>assets/js/script.js</code></li>";
echo "<li>Найдите строку: <code>fetch('send_message_fallback.php', {</code></li>";
echo "<li>Замените на: <code>fetch('send_message_direct_smtp.php', {</code></li>";
echo "<li>Сохраните файл</li>";
echo "</ul>";
echo "<li><strong>Протестируйте форму снова</strong> - теперь письма должны приходить</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";
echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
echo "</div>";
?>