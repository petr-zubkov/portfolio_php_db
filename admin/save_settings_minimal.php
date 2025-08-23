<?php
// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

session_start();
if (!isset($_SESSION['admin'])) {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

// Устанавливаем заголовок
header('Content-Type: application/json');

try {
    // Проверяем метод
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    // Получаем базовые данные
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 1;
    $site_title = isset($_POST['site_title']) ? $_POST['site_title'] : '';
    
    if (empty($site_title)) {
        throw new Exception('Site title is required');
    }
    
    // Проверяем подключение
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Самый простой UPDATE
    $sql = "UPDATE settings SET site_title = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('si', $site_title, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    // Очищаем буфер и выводим результат
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Settings saved',
        'affected' => $affected
    ]);
    
} catch (Exception $e) {
    // В случае ошибки - очищаем буфер и выводим ошибку
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
?>