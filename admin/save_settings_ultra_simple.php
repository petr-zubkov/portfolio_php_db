<?php
// Ультра-простая версия для проверки
session_start();
if (!isset($_SESSION['admin'])) {
    die('Access denied');
}

require_once '../config.php';

// Просто возвращаем успешный ответ без обновления БД
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Тестовый ответ (без обновления БД)',
    'post_data' => $_POST,
    'time' => date('Y-m-d H:i:s')
]);

exit;
?>