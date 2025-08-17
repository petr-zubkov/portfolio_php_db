<?php
require_once 'config.php';

echo "<h2>Установка базы данных</h2>";

// SQL для создания таблиц
$sql = [
    "CREATE TABLE IF NOT EXISTS `projects` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `image` varchar(255) NOT NULL,
      `link` varchar(255) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "CREATE TABLE IF NOT EXISTS `skills` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `icon` varchar(100) NOT NULL,
      `level` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "CREATE TABLE IF NOT EXISTS `contact` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(255) NOT NULL,
      `phone` varchar(50) NOT NULL,
      `telegram` varchar(100) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "CREATE TABLE IF NOT EXISTS `settings` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "INSERT INTO `settings` (`id`, `site_title`, `hero_title`, `hero_subtitle`, `avatar`, `about_text`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color`, `font_family`, `bg_image`, `experience_years`, `projects_count`, `clients_count`) VALUES
    (1, 'Портфолио верстальщика книг', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий', 'assets/img/placeholder.jpg', '', '#2c3e50', '#3498db', '#e74c3c', '#333333', '#ffffff', 'Roboto', '', 5, 100, 50);",

    "INSERT INTO `contact` (`id`, `email`, `phone`, `telegram`) VALUES
    (1, '', '', '');"
];

// Выполняем запросы
foreach ($sql as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Таблица успешно создана/обновлена<br>";
    } else {
        echo "Ошибка: " . $conn->error . "<br>";
    }
}

echo "<h3>Установка завершена!</h3>";
echo "<p><a href='index.php'>Перейти на сайт</a></p>";
echo "<p><a href='admin/auth.php'>Войти в админ-панель</a></p>";
?>