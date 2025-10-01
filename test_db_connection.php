<?php
// Тест подключения к базе данных с обработкой ошибок
echo "<h1>Тест подключения к базе данных</h1>";

// Конфигурация базы данных
$db_host = 'localhost';
$db_user = 'u188825_petr-zubkov';
$db_pass = '559-t7x-6vP-Dyu';
$db_name = 'u188825_portfolio_db';

echo "<p>Попытка подключения к базе данных...</p>";
echo "<p>Хост: $db_host</p>";
echo "<p>Пользователь: $db_user</p>";
echo "<p>База данных: $db_name</p>";

try {
    // Подключение к базе данных
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Ошибка подключения: " . $conn->connect_error . "</p>";
        echo "<p style='color: orange;'>Проверьте:</p>";
        echo "<ul>";
        echo "<li>Правильность данных для подключения</li>";
        echo "<li>Доступность сервера базы данных</li>";
        echo "<li>Существование базы данных</li>";
        echo "<li>Права пользователя</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>Подключение к базе данных успешно!</p>";
        
        // Установка кодировки
        $conn->set_charset("utf8");
        echo "<p>Кодировка установлена: utf8</p>";
        
        // Проверка таблиц
        echo "<h2>Проверка таблиц</h2>";
        $tables = ['projects', 'skills', 'contact', 'personal_info', 'themes', 'settings'];
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p style='color: green;'>Таблица '$table' существует</p>";
                
                // Получаем количество записей
                $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                if ($count_result) {
                    $row = $count_result->fetch_assoc();
                    echo "<p style='color: blue;'>Записей в таблице '$table': " . $row['count'] . "</p>";
                }
            } else {
                echo "<p style='color: red;'>Таблица '$table' не найдена</p>";
            }
        }
        
        // Закрываем соединение
        $conn->close();
        echo "<p>Соединение с базой данных закрыто</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Исключение: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='test_basic.php'>Базовый тест PHP</a></p>";
echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";
echo "<p><a href='update_database.php'>Обновить базу данных</a></p>";
?>