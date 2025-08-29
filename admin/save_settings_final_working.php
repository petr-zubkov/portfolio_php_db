<?php
// Финальная рабочая версия на основе успешных тестов
session_start();
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

header('Content-Type: application/json');

try {
    // Получаем данные из POST (как в успешном тесте)
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 1;
    $site_title = isset($_POST['site_title']) ? trim($_POST['site_title']) : '';
    $hero_title = isset($_POST['hero_title']) ? trim($_POST['hero_title']) : '';
    $hero_subtitle = isset($_POST['hero_subtitle']) ? trim($_POST['hero_subtitle']) : '';
    $about_text = isset($_POST['about_text']) ? trim($_POST['about_text']) : '';
    $experience_years = isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
    $projects_count = isset($_POST['projects_count']) ? (int)$_POST['projects_count'] : 0;
    $clients_count = isset($_POST['clients_count']) ? (int)$_POST['clients_count'] : 0;
    
    // Проверяем обязательные поля
    if (empty($site_title)) {
        throw new Exception('Название сайта обязательно');
    }
    
    // Выполняем SQL-запрос (как в успешном тесте через SQL Executor)
    $sql = "UPDATE settings SET 
        site_title = ?, 
        hero_title = ?, 
        hero_subtitle = ?,
        about_text = ?,
        experience_years = ?,
        projects_count = ?,
        clients_count = ?
        WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Ошибка подготовки запроса: ' . $conn->error);
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
        throw new Exception('Ошибка выполнения запроса: ' . $stmt->error);
    }
    
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    // Возвращаем успешный ответ
    echo json_encode([
        'success' => true,
        'message' => 'Настройки успешно сохранены (FINAL WORKING VERSION)',
        'affected_rows' => $affected,
        'debug_info' => [
            'site_title' => $site_title,
            'about_text_length' => strlen($about_text),
            'time' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    // Возвращаем ошибку
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
?>