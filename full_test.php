<?php
// Комплексный тест с сессиями и config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Full Test</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Комплексный тест</h1>";

// Тест сессий
echo "<h2>Тест сессий</h2>";
try {
    session_start();
    echo "<p style='color: green;'>✓ Session started successfully</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Session error: " . $e->getMessage() . "</p>";
}

// Тест подключения config.php
echo "<h2>Тест подключения config.php</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✓ Config file included successfully</p>";
    
    if (isset($conn)) {
        echo "<p style='color: green;'>✓ Database connection variable is set</p>";
        if ($conn->connect_error) {
            echo "<p style='color: orange;'>⚠ Database connection error: " . htmlspecialchars($conn->connect_error) . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Database connection successful!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection variable is not set</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Config include error: " . $e->getMessage() . "</p>";
}

echo "<h2>Дополнительная информация</h2>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Текущий файл: " . __FILE__ . "</p>";
echo "<p>Текущая директория: " . getcwd() . "</p>";

echo "<h2>Ссылки для тестирования</h2>";
echo "<p><a href='text_test.php'>Текстовый тест</a></p>";
echo "<p><a href='html_test.php'>HTML тест</a></p>";
echo "<p><a href='index_basic.php'>Базовая главная страница</a></p>";
echo "<p><a href='session_simple.php'>Session простой тест</a></p>";
echo "<p><a href='require_simple.php'>Require простой тест</a></p>";

echo "</body>";
echo "</html>";
?>