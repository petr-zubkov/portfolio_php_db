<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Тест подключения к БД</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; margin: 5px 0; border-radius: 5px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Тест подключения к базе данных</h1>';

// Тест 1: Подключение
echo '<div class="info">Тест 1: Подключение к базе данных</div>';
if ($conn && !$conn->connect_error) {
    echo '<div class="success">✓ Подключение успешно установлено</div>';
} else {
    echo '<div class="error">✗ Ошибка подключения: ' . ($conn->connect_error ?? 'Нет соединения') . '</div>';
}

// Тест 2: Проверка таблицы
echo '<div class="info">Тест 2: Проверка таблицы settings</div>';
$tableCheck = $conn->query("SHOW TABLES LIKE 'settings'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo '<div class="success">✓ Таблица settings существует</div>';
    
    // Тест 3: Структура таблицы
    echo '<div class="info">Тест 3: Структура таблицы</div>';
    $structure = $conn->query("DESCRIBE settings");
    if ($structure) {
        echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
        echo '<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Key</th><th>Default</th></tr>';
        while ($row = $structure->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['Field'] . '</td>';
            echo '<td>' . $row['Type'] . '</td>';
            echo '<td>' . $row['Null'] . '</td>';
            echo '<td>' . $row['Key'] . '</td>';
            echo '<td>' . $row['Default'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="error">✗ Ошибка получения структуры: ' . $conn->error . '</div>';
    }
    
    // Тест 4: Данные в таблице
    echo '<div class="info">Тест 4: Данные в таблице</div>';
    $dataCheck = $conn->query("SELECT * FROM settings LIMIT 1");
    if ($dataCheck && $dataCheck->num_rows > 0) {
        echo '<div class="success">✓ В таблице есть данные</div>';
        $data = $dataCheck->fetch_assoc();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    } else {
        echo '<div class="error">✗ В таблице нет данных</div>';
    }
    
    // Тест 5: Простой UPDATE
    echo '<div class="info">Тест 5: Простой UPDATE запрос</div>';
    try {
        $testUpdate = $conn->query("UPDATE settings SET site_title = 'Тест " . time() . "' WHERE id = 1");
        if ($testUpdate) {
            echo '<div class="success">✓ Простой UPDATE выполнен успешно</div>';
            echo '<div class="info">Затронуто строк: ' . $conn->affected_rows . '</div>';
        } else {
            echo '<div class="error">✗ Ошибка выполнения UPDATE: ' . $conn->error . '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Исключение: ' . $e->getMessage() . '</div>';
    }
    
} else {
    echo '<div class="error">✗ Таблица settings не существует</div>';
    
    // Пробуем создать таблицу
    echo '<div class="info">Попытка создать таблицу...</div>';
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
        `experience_years` int(11) NOT NULL,
        `projects_count` int(11) NOT NULL,
        `clients_count` int(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($createTable)) {
        echo '<div class="success">✓ Таблица успешно создана</div>';
        
        // Вставляем начальные данные
        $insertData = "INSERT INTO `settings` (`id`, `site_title`, `hero_title`, `hero_subtitle`, `avatar`, `about_text`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color`, `font_family`, `bg_image`, `experience_years`, `projects_count`, `clients_count`) VALUES
        (1, 'Портфолио верстальщика книг', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий', 'assets/img/placeholder.jpg', '', '#2c3e50', '#3498db', '#e74c3c', '#333333', '#ffffff', 'Roboto', '', 5, 100, 50)";
        
        if ($conn->query($insertData)) {
            echo '<div class="success">✓ Начальные данные вставлены</div>';
        } else {
            echo '<div class="error">✗ Ошибка вставки данных: ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="error">✗ Ошибка создания таблицы: ' . $conn->error . '</div>';
    }
}

echo '<br><a href="index.php">Вернуться в админ-панель</a>';
echo '</body></html>';
?>