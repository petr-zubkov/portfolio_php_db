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
    $site_title = isset($_POST['site_title']) ? trim($_POST['site_title']) : '';
    $hero_title = isset($_POST['hero_title']) ? trim($_POST['hero_title']) : '';
    $hero_subtitle = isset($_POST['hero_subtitle']) ? trim($_POST['hero_subtitle']) : '';
    $experience_years = isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
    $projects_count = isset($_POST['projects_count']) ? (int)$_POST['projects_count'] : 0;
    $clients_count = isset($_POST['clients_count']) ? (int)$_POST['clients_count'] : 0;
    
    // Проверяем обязательные поля
    if (empty($site_title)) {
        throw new Exception('Site title is required');
    }
    
    // Проверяем подключение
    if (!$conn || $conn->connect_error) {
        throw new Exception('Database connection failed');
    }
    
    // Обновляем данные
    $sql = "UPDATE settings SET 
        site_title = ?, 
        hero_title = ?, 
        hero_subtitle = ?,
        experience_years = ?,
        projects_count = ?,
        clients_count = ?
        WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("sssiiii", 
        $site_title, 
        $hero_title, 
        $hero_subtitle,
        $experience_years,
        $projects_count,
        $clients_count,
        $id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    // Очищаем буфер и выводим результат
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Настройки успешно сохранены',
        'affected_rows' => $affected
    ]);
    
} catch (Exception $e) {
    // Очищаем буфер и выводим ошибку
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
?>