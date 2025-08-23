<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

// Прямой тест без формы
header('Content-Type: application/json');

try {
    $sql = "UPDATE settings SET site_title = 'Direct Test " . time() . "' WHERE id = 1";
    $result = $conn->query($sql);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Direct update successful',
            'affected' => $conn->affected_rows
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Direct update failed: ' . $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>