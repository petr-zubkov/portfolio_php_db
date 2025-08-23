<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

require_once '../config.php';

echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест настроек</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Тестирование настроек</h1>
        <a href="index.php" class="btn btn-secondary">Назад</a>
        
        <div class="mt-4">';

// Тест 1: Проверка подключения к БД
echo '<h3>Тест 1: Подключение к БД</h3>';
if ($conn) {
    echo '<div class="alert alert-success">✓ Подключение к БД успешно</div>';
} else {
    echo '<div class="alert alert-danger">✗ Ошибка подключения к БД</div>';
}

// Тест 2: Проверка таблицы settings
echo '<h3>Тест 2: Таблица settings</h3>';
$tableCheck = $conn->query("SHOW TABLES LIKE 'settings'");
if ($tableCheck->num_rows > 0) {
    echo '<div class="alert alert-success">✓ Таблица settings существует</div>';
    
    // Проверяем структуру
    $structure = $conn->query("DESCRIBE settings");
    echo '<h4>Структура таблицы:</h4>';
    echo '<table class="table table-bordered">';
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
} else {
    echo '<div class="alert alert-danger">✗ Таблица settings не существует</div>';
}

// Тест 3: Проверка данных в таблице
echo '<h3>Тест 3: Данные в таблице settings</h3>';
$dataCheck = $conn->query("SELECT * FROM settings");
if ($dataCheck->num_rows > 0) {
    echo '<div class="alert alert-success">✓ В таблице есть данные (' . $dataCheck->num_rows . ' записей)</div>';
    echo '<h4>Текущие настройки:</h4>';
    echo '<pre>';
    while ($row = $dataCheck->fetch_assoc()) {
        print_r($row);
    }
    echo '</pre>';
} else {
    echo '<div class="alert alert-warning">⚠ В таблице нет данных</div>';
}

// Тест 4: Права на запись
echo '<h3>Тест 4: Права на запись</h3>';
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    if (mkdir($logDir, 0755, true)) {
        echo '<div class="alert alert-success">✓ Папка logs создана</div>';
    } else {
        echo '<div class="alert alert-danger">✗ Не удалось создать папку logs</div>';
    }
} else {
    echo '<div class="alert alert-success">✓ Папка logs существует</div>';
}

$logFile = $logDir . '/test.log';
if (file_put_contents($logFile, 'Тест записи')) {
    echo '<div class="alert alert-success">✓ Запись в файл успешна</div>';
    unlink($logFile);
} else {
    echo '<div class="alert alert-danger">✗ Ошибка записи в файл</div>';
}

echo '</div></body></html>';
?>