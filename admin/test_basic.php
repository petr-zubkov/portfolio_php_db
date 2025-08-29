<?php
// Простейший тестовый файл
header('Content-Type: application/json');

try {
    // Простая проверка
    $response = [
        'success' => true,
        'message' => 'Базовый тест работает',
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>