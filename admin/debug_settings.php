<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

echo "<h2>Отладка настроек</h2>";

// Проверяем подключение к БД
if (!$conn) {
    die("Ошибка подключения к БД");
}

// Проверяем таблицу settings
$result = $conn->query("SHOW TABLES LIKE 'settings'");
if ($result->num_rows == 0) {
    die("Таблица settings не существует");
}

// Получаем текущие настройки
$result = $conn->query("SELECT * FROM settings LIMIT 1");
if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
    echo "<h3>Текущие настройки в БД:</h3>";
    echo "<pre>";
    print_r($settings);
    echo "</pre>";
} else {
    echo "<p>В таблице settings нет записей</p>";
}

// Проверяем POST-данные
echo "<h3>POST данные:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Проверяем ошибки
echo "<h3>Ошибки:</h3>";
echo "<pre>";
print_r(error_get_last());
echo "</pre>";
?>