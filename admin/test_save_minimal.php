<?php
// Минимальный тест сохранения без сессии и базы данных
header('Content-Type: application/json');

try {
    // Просто получаем POST данные и возвращаем их
    $postData = $_POST;
    
    $response = [
        'success' => true,
        'message' => 'Минимальный тест сохранения работает',
        'post_data' => $postData,
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