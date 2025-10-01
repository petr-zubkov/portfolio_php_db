<?php
// Проверка файла config.php
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Config Test</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Тест файла config.php</h1>";

// Проверяем существование файла
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ Файл config.php существует</p>";
    
    // Проверяем размер файла
    $filesize = filesize('config.php');
    echo "<p>Размер файла: $filesize байт</p>";
    
    // Проверяем права доступа
    $perms = fileperms('config.php');
    echo "<p>Права доступа: " . substr(sprintf('%o', $perms), -4) . "</p>";
    
    // Пробуем прочитать файл
    $content = file_get_contents('config.php');
    if ($content !== false) {
        echo "<p style='color: green;'>✓ Файл успешно прочитан</p>";
        echo "<p>Длина содержимого: " . strlen($content) . " символов</p>";
        
        // Показываем первые 500 символов для проверки
        echo "<h3>Первые 500 символов файла:</h3>";
        echo "<pre style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";
        
        // Проверяем синтаксис PHP
        echo "<h3>Проверка синтаксиса:</h3>";
        $tokens = @token_get_all($content);
        if ($tokens === false) {
            echo "<p style='color: red;'>✗ Ошибка синтаксиса PHP в файле config.php</p>";
        } else {
            echo "<p style='color: green;'>✓ Синтаксис PHP корректен</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Не удалось прочитать файл config.php</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Файл config.php не найден</p>";
}

echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "</body>";
echo "</html>";
?>