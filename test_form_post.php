<?php
// Тестовый файл для проверки POST запросов
header('Content-Type: application/json; charset=utf-8');

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

try {
    // Проверяем метод запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен. Используйте POST.');
    }

    // Получаем данные
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Возвращаем полученные данные
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'POST запрос получен успешно',
        'data' => [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST
        ]
    ]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST
    ]);
}

exit;
?>