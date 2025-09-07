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

// Настройки почты
define('SMTP_HOST', 'smtp.mail.ru');
define('SMTP_PORT', 465);
define('SMTP_USER', 'petr-zubkov@mail.ru');
define('SMTP_PASS', '2zVHAaCehztVoZIoi9U0'); // Замените на ваш пароль
define('SMTP_FROM', 'petr-zubkov@mail.ru');
define('SMTP_FROM_NAME', 'Пётр Зубков');
define('SMTP_TO_EMAIL', 'petr-zubkov@mail.ru');

// Настройки сайта
define('SITE_URL', 'https://zubkov.space');
define('ADMIN_EMAIL', 'petr-zubkov@mail.ru');

// Создаем папку для загрузок если ее нет
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Функция для преобразования hex в rgb
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    
    return "$r,$g,$b";
}

// Запуск сессии
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>