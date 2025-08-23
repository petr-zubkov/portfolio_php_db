<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

header('Content-Type: application/json');

try {
    // Самый простой UPDATE
    $result = $conn->query("UPDATE settings SET site_title = 'Тест " . time() . "' WHERE id = 1");
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Простой UPDATE выполнен',
            'affected_rows' => $conn->affected_rows
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка UPDATE: ' . $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>