<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка таблицы settings</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 5px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 5px 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Проверка таблицы settings</h1>';

// Проверяем существование таблицы
$tableCheck = $conn->query("SHOW TABLES LIKE 'settings'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo '<div class="success">✓ Таблица settings существует</div>';
    
    // Проверяем структуру
    echo '<h3>Структура таблицы:</h3>';
    $structure = $conn->query("DESCRIBE settings");
    if ($structure) {
        echo '<table>';
        echo '<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
        while ($row = $structure->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['Field'] . '</td>';
            echo '<td>' . $row['Type'] . '</td>';
            echo '<td>' . $row['Null'] . '</td>';
            echo '<td>' . $row['Key'] . '</td>';
            echo '<td>' . $row['Default'] . '</td>';
            echo '<td>' . $row['Extra'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // Проверяем данные
    echo '<h3>Данные в таблице:</h3>';
    $dataCheck = $conn->query("SELECT * FROM settings");
    if ($dataCheck && $dataCheck->num_rows > 0) {
        echo '<table>';
        echo '<tr><th>ID</th><th>Site Title</th><th>Hero Title</th><th>Experience Years</th><th>Projects Count</th><th>Clients Count</th></tr>';
        while ($row = $dataCheck->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['site_title'] . '</td>';
            echo '<td>' . $row['hero_title'] . '</td>';
            echo '<td>' . $row['experience_years'] . '</td>';
            echo '<td>' . $row['projects_count'] . '</td>';
            echo '<td>' . $row['clients_count'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="error">✗ В таблице нет данных</div>';
    }
    
    // Пробуем простой UPDATE
    echo '<h3>Тест UPDATE:</h3>';
    try {
        $testUpdate = $conn->query("UPDATE settings SET site_title = 'Тест " . time() . "' WHERE id = 1");
        if ($testUpdate) {
            echo '<div class="success">✓ UPDATE выполнен успешно</div>';
            echo '<div>Затронуто строк: ' . $conn->affected_rows . '</div>';
        } else {
            echo '<div class="error">✗ Ошибка UPDATE: ' . $conn->error . '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Исключение: ' . $e->getMessage() . '</div>';
    }
    
} else {
    echo '<div class="error">✗ Таблица settings не существует</div>';
    
    // Создаем таблицу
    echo '<h3>Создание таблицы...</h3>';
    $createTable = "CREATE TABLE `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `site_title` varchar(255) NOT NULL,
        `hero_title` varchar(255) NOT NULL,
        `hero_subtitle` text NOT NULL,
        `avatar` varchar(255) NOT NULL,
        `about_text` text NOT NULL,
        `primary_color` varchar(7) NOT NULL,
        `secondary_color` varchar(7) NOT NULL,
        `accent_color` varchar(7) NOT NULL,
        `text_color` varchar(7) NOT NULL,
        `bg_color` varchar(7) NOT NULL,
        `font_family` varchar(50) NOT NULL,
        `bg_image` varchar(255) NOT NULL,
        `experience_years` int(11) NOT NULL DEFAULT 0,
        `projects_count` int(11) NOT NULL DEFAULT 0,
        `clients_count` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($createTable)) {
        echo '<div class="success">✓ Таблица создана</div>';
        
        // Вставляем данные
        $insertData = "INSERT INTO `settings` (`id`, `site_title`, `hero_title`, `hero_subtitle`, `avatar`, `about_text`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color`, `font_family`, `bg_image`, `experience_years`, `projects_count`, `clients_count`) VALUES
        (1, 'Портфолио верстальщика книг', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий', 'assets/img/placeholder.jpg', '', '#2c3e50', '#3498db', '#e74c3c', '#333333', '#ffffff', 'Roboto', '', 5, 100, 50)";
        
        if ($conn->query($insertData)) {
            echo '<div class="success">✓ Данные вставлены</div>';
        } else {
            echo '<div class="error">✗ Ошибка вставки: ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="error">✗ Ошибка создания таблицы: ' . $conn->error . '</div>';
    }
}

echo '<br><a href="index.php">Вернуться в админ-панель</a>';
echo '</body></html>';
?>