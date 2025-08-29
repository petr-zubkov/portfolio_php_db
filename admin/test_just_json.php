<?php
// Самый простой тест - просто вернуть JSON
header('Content-Type: application/json');

try {
    $response = [
        'success' => true,
        'message' => 'Простой JSON работает',
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