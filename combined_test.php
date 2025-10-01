<?php
// Тест сессий и подключения файлов
session_start();
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Комбинированный тест</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Комбинированный тест (сессии + подключение файлов)</h1>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>ID сессии: " . session_id() . "</p>";

// Пробуем подключить config.php
echo "<h2>Тест подключения config.php</h2>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ Файл config.php существует</p>";
    
    ob_start();
    try {
        $include_result = include_once 'config.php';
        $include_output = ob_get_clean();
        
        if ($include_result !== false) {
            echo "<p style='color: green;'>✓ Файл config.php успешно включен</p>";
            
            // Проверяем базовые переменные
            if (isset($conn)) {
                echo "<p style='color: green;'>✓ Переменная \$conn установлена</p>";
                if ($conn->connect_error) {
                    echo "<p style='color: orange;'>⚠ Ошибка подключения к базе данных: " . htmlspecialchars($conn->connect_error) . "</p>";
                } else {
                    echo "<p style='color: green;'>✓ Подключение к базе данных успешно</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Переменная \$conn не установлена</p>";
            }
            
        } else {
            echo "<p style='color: red;'>✗ Ошибка при включении config.php</p>";
        }
        
        if (!empty($include_output)) {
            echo "<p>Вывод при включении: " . htmlspecialchars($include_output) . "</p>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p style='color: red;'>✗ Исключение при включении config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Файл config.php не найден</p>";
}

echo "<hr>";
echo "<h2>Ссылки для тестирования</h2>";
echo "<p><a href='text_test.php'>Текстовый тест</a></p>";
echo "<p><a href='html_test.php'>HTML тест</a></p>";
echo "<p><a href='session_test.php'>Тест сессий</a></p>";
echo "<p><a href='include_test.php'>Тест подключения файлов</a></p>";
echo "<p><a href='index_basic.php'>Базовая главная страница</a></p>";

echo "</body>";
echo "</html>";
?>