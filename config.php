<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'u188825_petr-zubkov');
define('DB_PASS', '559-t7x-6vP-Dyu');
define('DB_NAME', 'u188825_portfolio_db');

// Подключение к базе данных
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Установка кодировки
$conn->set_charset("utf8");

// Путь для загрузки изображений
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', '/uploads/');

// Создаем папку для загрузок если ее нет
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
?>