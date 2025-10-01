<?php
require_once 'config.php';

echo "Обновление базы данных...<br>";

try {
    // Добавляем новые поля в таблицу personal_info
    $sql = "ALTER TABLE `personal_info` 
            ADD COLUMN IF NOT EXISTS `hobbies` text NOT NULL AFTER `social_links`,
            ADD COLUMN IF NOT EXISTS `favorite_movies` text NOT NULL AFTER `hobbies`,
            ADD COLUMN IF NOT EXISTS `my_books` text NOT NULL AFTER `favorite_movies`,
            ADD COLUMN IF NOT EXISTS `websites` text NOT NULL AFTER `my_books`";
    
    if ($conn->query($sql)) {
        echo "Новые поля успешно добавлены в таблицу personal_info<br>";
    } else {
        echo "Ошибка при добавлении полей: " . $conn->error . "<br>";
    }
    
    // Обновляем существующую запись с примерами данных
    $update_sql = "UPDATE `personal_info` SET 
        `hobbies` = '[\"Чтение научной фантастики\", \"Программирование\", \"Фотография\", \"Путешествия\", \"Астрономия\"]',
        `favorite_movies` = '[\"Интерстеллар\", \"Матрица\", \"Начало\", \"Марсианин\", \"Гравитация\"]',
        `my_books` = '[\"Космос Карла Сагана\", \"1984 Джорджа Оруэлла\", \"Дюна Фрэнка Герберта\", \"Мастер и Маргарита\", \"Three Body Problem\"]',
        `websites` = '[{\"name\":\"GitHub\",\"url\":\"https://github.com/petr-zubkov\"},{\"name\":\"LinkedIn\",\"url\":\"https://linkedin.com/in/petr-zubkov\"},{\"name\":\"Блог\",\"url\":\"https://blog.zubkov.space\"}]'
        WHERE `id` = 1";
    
    if ($conn->query($update_sql)) {
        echo "Данные успешно обновлены<br>";
    } else {
        echo "Ошибка при обновлении данных: " . $conn->error . "<br>";
    }
    
    echo "<br>База данных успешно обновлена!";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}

$conn->close();
?>