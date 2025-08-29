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
    $action = $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Action не указан');
    }
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $icon = $_POST['icon'] ?? '';
            $level = $_POST['level'] ?? 0;
            
            if (!empty($name) && !empty($icon) && !empty($level)) {
                $stmt = $conn->prepare("INSERT INTO skills (name, icon, level) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $name, $icon, $level);
                $stmt->execute();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Навык успешно добавлен']);
            } else {
                throw new Exception('Заполните все поля');
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $icon = $_POST['icon'] ?? '';
            $level = $_POST['level'] ?? 0;
            
            if (!empty($name) && !empty($icon) && !empty($level)) {
                $stmt = $conn->prepare("UPDATE skills SET name = ?, icon = ?, level = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $icon, $level, $id);
                $stmt->execute();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Навык успешно обновлен']);
            } else {
                throw new Exception('Заполните все поля');
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Навык успешно удален']);
            break;
            
        default:
            throw new Exception('Неизвестный action: ' . $action);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

exit;
?>