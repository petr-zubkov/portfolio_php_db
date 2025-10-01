<?php
require_once 'config.php';

echo "<h1>Проверка базы данных</h1>";

// Проверяем таблицу personal_info
$result = $conn->query("SELECT * FROM personal_info LIMIT 1");
if ($result) {
    $personal_info = $result->fetch_assoc();
    if ($personal_info) {
        echo "<h2>Данные из таблицы personal_info:</h2>";
        echo "<pre>";
        print_r($personal_info);
        echo "</pre>";
        
        // Декодируем social_links
        if (!empty($personal_info['social_links'])) {
            $social_links = json_decode($personal_info['social_links'], true);
            echo "<h3>Социальные ссылки (декодированные):</h3>";
            echo "<pre>";
            print_r($social_links);
            echo "</pre>";
        }
    } else {
        echo "<p>Таблица personal_info пуста</p>";
    }
} else {
    echo "<p>Ошибка при получении данных из personal_info: " . $conn->error . "</p>";
}

// Проверяем другие таблицы
echo "<h2>Структура таблиц:</h2>";

$tables = ['personal_info', 'projects', 'skills', 'contact', 'themes', 'settings'];
foreach ($tables as $table) {
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        echo "<h3>Таблица: $table</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Ключ</th><th>По умолчанию</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Ошибка при описании таблицы $table: " . $conn->error . "</p>";
    }
}
?>