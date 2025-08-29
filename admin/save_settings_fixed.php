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

// Функция логирования
function logMessage($message) {
    $logFile = __DIR__ . '/logs/settings.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Устанавливаем заголовок
header('Content-Type: application/json');

try {
    logMessage("=== Начало обработки save_settings_fixed.php ===");
    
    // Проверяем метод
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        logMessage("Метод не POST: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Only POST method allowed');
    }
    
    logMessage("Метод: POST");
    
    // Проверяем POST данные
    logMessage("POST данные: " . json_encode($_POST));
    
    if (empty($_POST)) {
        logMessage("POST данные пустые");
        throw new Exception('No POST data received');
    }
    
    // Получаем и валидируем все поля
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 1;
    $site_title = filter_input(INPUT_POST, 'site_title', FILTER_SANITIZE_STRING) ?: '';
    $hero_title = filter_input(INPUT_POST, 'hero_title', FILTER_SANITIZE_STRING) ?: '';
    $hero_subtitle = filter_input(INPUT_POST, 'hero_subtitle', FILTER_SANITIZE_STRING) ?: '';
    $about_text = filter_input(INPUT_POST, 'about_text', FILTER_SANITIZE_STRING) ?: '';
    $avatar = filter_input(INPUT_POST, 'avatar', FILTER_SANITIZE_STRING) ?: '';
    $primary_color = filter_input(INPUT_POST, 'primary_color', FILTER_SANITIZE_STRING) ?: '#2c3e50';
    $secondary_color = filter_input(INPUT_POST, 'secondary_color', FILTER_SANITIZE_STRING) ?: '#3498db';
    $accent_color = filter_input(INPUT_POST, 'accent_color', FILTER_SANITIZE_STRING) ?: '#e74c3c';
    $text_color = filter_input(INPUT_POST, 'text_color', FILTER_SANITIZE_STRING) ?: '#333333';
    $bg_color = filter_input(INPUT_POST, 'bg_color', FILTER_SANITIZE_STRING) ?: '#ffffff';
    $font_family = filter_input(INPUT_POST, 'font_family', FILTER_SANITIZE_STRING) ?: 'Roboto';
    $bg_image = filter_input(INPUT_POST, 'bg_image', FILTER_SANITIZE_STRING) ?: '';
    $experience_years = isset($_POST['experience_years']) ? (int)$_POST['experience_years'] : 0;
    $projects_count = isset($_POST['projects_count']) ? (int)$_POST['projects_count'] : 0;
    $clients_count = isset($_POST['clients_count']) ? (int)$_POST['clients_count'] : 0;
    
    logMessage("ID: $id");
    logMessage("Site Title: $site_title");
    logMessage("Hero Title: $hero_title");
    logMessage("About Text length: " . strlen($about_text));
    
    if (empty($site_title)) {
        logMessage("Пустой site_title");
        throw new Exception('Site title is required');
    }
    
    // Проверяем подключение
    if (!$conn || $conn->connect_error) {
        logMessage("Ошибка подключения: " . ($conn->connect_error ?? 'Unknown'));
        throw new Exception('Database connection failed');
    }
    
    logMessage("Подключение к БД OK");
    
    // Начинаем транзакцию
    $conn->begin_transaction();
    logMessage("Транзакция начата");
    
    try {
        // Обновляем все поля
        $sql = "UPDATE settings SET 
            site_title = ?, 
            hero_title = ?, 
            hero_subtitle = ?,
            about_text = ?,
            avatar = ?,
            primary_color = ?,
            secondary_color = ?,
            accent_color = ?,
            text_color = ?,
            bg_color = ?,
            font_family = ?,
            bg_image = ?,
            experience_years = ?,
            projects_count = ?,
            clients_count = ?
            WHERE id = ?";
        
        logMessage("SQL: $sql");
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            logMessage("Ошибка prepare: " . $conn->error);
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        logMessage("Параметры: experience_years=$experience_years, projects_count=$projects_count, clients_count=$clients_count");
        
        $stmt->bind_param("sssssssssssiiii", 
            $site_title, 
            $hero_title, 
            $hero_subtitle,
            $about_text,
            $avatar,
            $primary_color,
            $secondary_color,
            $accent_color,
            $text_color,
            $bg_color,
            $font_family,
            $bg_image,
            $experience_years,
            $projects_count,
            $clients_count,
            $id
        );
        
        if (!$stmt->execute()) {
            logMessage("Ошибка execute: " . $stmt->error);
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $affected = $stmt->affected_rows;
        logMessage("UPDATE выполнен, затронуто строк: $affected");
        
        $stmt->close();
        
        // Завершаем транзакцию
        $conn->commit();
        logMessage("Транзакция завершена");
        
        // Очищаем буфер
        ob_end_clean();
        
        $response = [
            'success' => true,
            'message' => 'Настройки успешно сохранены',
            'affected_rows' => $affected
        ];
        
        logMessage("Успешный ответ: " . json_encode($response));
        echo json_encode($response);
        
    } catch (Exception $e) {
        // Откатываем транзакцию
        $conn->rollback();
        logMessage("Транзакция откачена: " . $e->getMessage());
        throw $e;
    }
    
} catch (Exception $e) {
    logMessage("Фатальная ошибка: " . $e->getMessage());
    
    // Очищаем буфер
    ob_end_clean();
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    logMessage("Ошибка ответа: " . json_encode($response));
    echo json_encode($response);
}

// Закрываем соединение
if (isset($conn)) {
    $conn->close();
}

logMessage("=== Завершение обработки ===");
exit;
?>