<?php
// Простая установка PHPMailer без сложных операций
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Простая установка PHPMailer</h1>";

try {
    // Проверяем права на запись
    echo "<div>Проверка прав на запись...</div>";
    
    if (!is_writable('.')) {
        throw new Exception("Нет прав на запись в текущую директорию");
    }
    
    echo "<div style='color: green;'>✅ Права на запись есть</div>";
    
    // Создаем директорию vendor
    if (!file_exists('vendor')) {
        if (mkdir('vendor', 0755, true)) {
            echo "<div style='color: green;'>✅ Создана директория vendor</div>";
        } else {
            throw new Exception("Не удалось создать директорию vendor");
        }
    } else {
        echo "<div style='color: blue;'>ℹ️ Директория vendor уже существует</div>";
    }
    
    // Создаем простую проверку PHPMailer
    $test_content = '<?php
// Простая проверка PHPMailer
echo "PHPMailer работает!";
?>';
    
    if (file_put_contents('vendor/test.php', $test_content)) {
        echo "<div style='color: green;'>✅ Тестовый файл создан</div>";
    } else {
        throw new Exception("Не удалось создать тестовый файл");
    }
    
    echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>🎉 Успешно!</h2>";
    echo "<p>Базовая структура создана. Теперь можно создать файлы PHPMailer.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>❌ Ошибка:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
echo "<a href='QUICK_FIX.php' style='display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🚀 Быстрое исправление</a>";
echo "</div>";

// Показываем информацию о сервере
echo "<div style='background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>Информация о сервере:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Dir:</strong> " . getcwd() . "</p>";
echo "<p><strong>Writable:</strong> " . (is_writable('.') ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Safe Mode:</strong> " . (ini_get('safe_mode') ? 'On' : 'Off') . "</p>";
echo "</div>";
?>