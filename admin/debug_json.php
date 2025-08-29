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
    // Простая проверка подключения
    if (!$conn || $conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    // Очищаем буфер
    ob_end_clean();
    
    // Возвращаем простой JSON для теста
    echo json_encode([
        'success' => true,
        'message' => 'JSON работает корректно',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
    ]);
    
} catch (Exception $e) {
    // Очищаем буфер
    ob_end_clean();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
?>