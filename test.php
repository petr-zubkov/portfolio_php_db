<?php
// Простая тестовая страница для проверки работы PHP
echo "<h1>Тестовая страница PHP</h1>";
echo "<p>Если вы видите эту страницу, PHP работает корректно.</p>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Версия PHP: " . phpversion() . "</p>";

// Проверка подключения к базе данных
try {
    // Сначала проверяем существование config.php
    if (!file_exists('config.php')) {
        echo "<p style='color: red;'>Файл config.php не найден</p>";
    } else {
        require_once 'config.php';
        
        // Проверяем, установлено ли соединение
        if (!isset($conn) || $conn->connect_error) {
            echo "<p style='color: red;'>Ошибка подключения к базе данных: " . 
                 (isset($conn) ? $conn->connect_error : "Соединение не установлено") . "</p>";
        } else {
            echo "<p style='color: green;'>Подключение к базе данных успешно!</p>";
            
            // Проверка основных таблиц
            $tables = ['projects', 'skills', 'contact', 'personal_info', 'themes', 'settings'];
            foreach ($tables as $table) {
                try {
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    if ($result && $result->num_rows > 0) {
                        echo "<p style='color: green;'>Таблица '$table' существует</p>";
                    } else {
                        echo "<p style='color: orange;'>Таблица '$table' не найдена</p>";
                    }
                } catch (Exception $e) {
                    echo "<p style='color: red;'>Ошибка при проверке таблицы '$table': " . $e->getMessage() . "</p>";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";
echo "<p><a href='update_database.php'>Обновить базу данных</a></p>";
?>