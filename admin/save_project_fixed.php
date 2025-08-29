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
    // Проверяем наличие action
    $action = $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Action не указан');
    }
    
    switch ($action) {
        case 'add':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $link = $_POST['link'] ?? '';
            $image_url = $_POST['image_url'] ?? '';
            
            $image_path = '';
            
            // Загрузка изображения если есть
            if (!empty($_FILES['image']['name'])) {
                $file_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = UPLOAD_PATH . $file_name;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = UPLOAD_URL . $file_name;
                }
            } elseif (!empty($image_url)) {
                $image_path = $image_url;
            }
            
            if (!empty($title) && !empty($description) && !empty($link) && !empty($image_path)) {
                $stmt = $conn->prepare("INSERT INTO projects (title, description, image, link) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $description, $image_path, $link);
                $stmt->execute();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Проект успешно добавлен']);
            } else {
                throw new Exception('Заполните все поля');
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $link = $_POST['link'] ?? '';
            $image_url = $_POST['image_url'] ?? '';
            
            // Получаем текущее изображение
            $stmt = $conn->prepare("SELECT image FROM projects WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_image = $result->fetch_assoc()['image'];
            
            $image_path = $current_image;
            
            // Загрузка нового изображения если есть
            if (!empty($_FILES['image']['name'])) {
                $file_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = UPLOAD_PATH . $file_name;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = UPLOAD_URL . $file_name;
                    
                    // Удаляем старое изображение если оно локальное
                    if (strpos($current_image, UPLOAD_URL) === 0) {
                        $old_file = UPLOAD_PATH . basename($current_image);
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                }
            } elseif (!empty($image_url)) {
                $image_path = $image_url;
            }
            
            if (!empty($title) && !empty($description) && !empty($link) && !empty($image_path)) {
                $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, image = ?, link = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $description, $image_path, $link, $id);
                $stmt->execute();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Проект успешно обновлен']);
            } else {
                throw new Exception('Заполните все поля');
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            // Получаем изображение для удаления
            $stmt = $conn->prepare("SELECT image FROM projects WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $image = $result->fetch_assoc()['image'];
            
            // Удаляем изображение если оно локальное
            if (strpos($image, UPLOAD_URL) === 0) {
                $file = UPLOAD_PATH . basename($image);
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            // Удаляем запись из БД
            $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Проект успешно удален']);
            break;
            
        default:
            throw new Exception('Неизвестный action: ' . $action);
    }
    
} catch (Exception $e) {
    // Очищаем буфер и выводим ошибку
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

exit;
?>