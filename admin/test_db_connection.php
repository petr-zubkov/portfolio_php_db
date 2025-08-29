<?php
// Тест подключения к базе данных
header('Content-Type: application/json');

try {
    require_once '../config.php';
    
    if (!$conn) {
        throw new Exception('Не удалось подключиться к базе данных');
    }
    
    $response = [
        'success' => true,
        'message' => 'Подключение к базе данных работает',
        'database' => 'u188825_portfolio_db',
        'time' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>