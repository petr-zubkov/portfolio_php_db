<?php
// Тест сессии
session_start();

header('Content-Type: application/json');

try {
    $isLoggedIn = isset($_SESSION['admin']);
    
    $response = [
        'success' => true,
        'message' => 'Тест сессии работает',
        'logged_in' => $isLoggedIn,
        'session_id' => session_id(),
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