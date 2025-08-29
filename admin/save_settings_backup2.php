<?php
// Включаем отображение всех ошибок для диагностики
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

// Устанавливаем заголовок
header('Content-Type: application/json');

try {
    // Логируем начало
    error_log("=== Начало обработки debug_response.php ===");
    
    // Проверяем метод
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    // Логируем POST данные
    error_log("POST данные: " . json_encode($_POST));
    
    // Проверяем подключение к БД
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    error_log("Подключение к БД успешно");
    
    // Получаем данные
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 1;
    $site_title = isset($_POST['site_title']) ? trim($_POST['site_title']) : '';
    $hero_title = isset($_POST['hero_title']) ? trim($_POST['hero_title']) : '';
    $hero_subtitle = isset($_POST['hero_subtitle']) ? trim($_POST['hero_subtitle']) : '';
    $about_text = isset($_POST['about_text']) ? trim($_POST['about_text']) : '';
    $experience_years = isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
    $projects_count = isset($_POST['projects_count']) ? (int)$_POST['projects_count'] : 0;
    $clients_count = isset($_POST['clients_count']) ? (int)$_POST['clients_count'] : 0;
    
    error_log("Данные получены: site_title=$site_title, about_text_length=" . strlen($about_text));
    
    // Выполняем запрос
    $sql = "UPDATE settings SET 
        site_title = ?, 
        hero_title = ?, 
        hero_subtitle = ?,
        about_text = ?,
        experience_years = ?,
        projects_count = ?,
        clients_count = ?
        WHERE id = ?";
    
    error_log("SQL: $sql");
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("ssssiiii", 
        $site_title, 
        $hero_title, 
        $hero_subtitle,
        $about_text,
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
    
    error_log("UPDATE выполнен успешно, affected_rows: $affected");
    
    // Формируем ответ
    $response = [
        'success' => true,
        'message' => 'Настройки успешно сохранены (DEBUG VERSION)',
        'affected_rows' => $affected,
        'debug_info' => [
            'post_data' => $_POST,
            'sql_executed' => $sql,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
    
    error_log("Отправляем ответ: " . json_encode($response));
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Ошибка: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ];
    
    echo json_encode($response);
}

error_log("=== Завершение обработки debug_response.php ===");
exit;
?>