<?php
// Тест подключения внешних файлов
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Тест подключения файлов</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Тест подключения внешних файлов</h1>";
echo "<p>Проверяем существование config.php...</p>";

if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ Файл config.php существует</p>";
    
    // Пробуем прочитать файл
    $config_content = file_get_contents('config.php');
    if ($config_content !== false) {
        echo "<p style='color: green;'>✓ Файл config.php успешно прочитан</p>";
        echo "<p>Размер файла: " . strlen($config_content) . " байт</p>";
        
        // Пробуем включить файл с буферизацией
        echo "<p>Пробуем включить config.php...</p>";
        ob_start();
        try {
            $include_result = include_once 'config.php';
            $include_output = ob_get_clean();
            
            if ($include_result !== false) {
                echo "<p style='color: green;'>✓ Файл config.php успешно включен</p>";
                
                // Проверяем константы
                if (defined('DB_HOST')) {
                    echo "<p style='color: green;'>✓ Константа DB_HOST определена</p>";
                } else {
                    echo "<p style='color: red;'>✗ Константа DB_HOST не определена</p>";
                }
                
                if (defined('DB_USER')) {
                    echo "<p style='color: green;'>✓ Константа DB_USER определена</p>";
                } else {
                    echo "<p style='color: red;'>✗ Константа DB_USER не определена</p>";
                }
                
                if (defined('DB_NAME')) {
                    echo "<p style='color: green;'>✓ Константа DB_NAME определена</p>";
                } else {
                    echo "<p style='color: red;'>✗ Константа DB_NAME не определена</p>";
                }
                
                // Проверяем переменную $conn
                if (isset($conn)) {
                    echo "<p style='color: green;'>✓ Переменная \$conn установлена</p>";
                    if ($conn->connect_error) {
                        echo "<p style='color: red;'>✗ Ошибка подключения: " . htmlspecialchars($conn->connect_error) . "</p>";
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
            echo "<p style='color: red;'>✗ Исключение: " . htmlspecialchars($e->getMessage()) . "</p>";
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