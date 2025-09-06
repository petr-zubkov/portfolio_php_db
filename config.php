<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'user_db');
define('DB_PASS', 'db_pass');
define('DB_NAME', 'db_name');

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

// Настройки почты
define('SMTP_HOST', 'smtp.mail.ru');
define('SMTP_PORT', 465);
define('SMTP_USER', 'test@mail.ru');
define('SMTP_PASS', 'psw'); // Замените на ваш пароль
define('SMTP_FROM', 'post@mail.ru');
define('SMTP_FROM_NAME', 'ИМЯ');
define('SMTP_TO_EMAIL', 'adresat@mail.ru');

// Настройки сайта
define('SITE_URL', 'https://site.xxx');
define('ADMIN_EMAIL', 'adres@mail.ru');

// Создаем папку для загрузок если ее нет
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
?>
