<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

$logFile = __DIR__ . '/error_log.txt';

if (file_exists($logFile)) {
    unlink($logFile);
    echo "Лог-файл очищен";
} else {
    echo "Лог-файл не найден";
}

echo '<br><a href="debug_response.php">Назад</a>';
?>