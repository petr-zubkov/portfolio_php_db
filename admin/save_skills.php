<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $name = $_POST['name'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $level = $_POST['level'] ?? 0;
        
        if (!empty($name) && !empty($icon) && !empty($level)) {
            $stmt = $conn->prepare("INSERT INTO skills (name, icon, level) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $icon, $level);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
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
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
        }
        break;
        
    case 'delete':
        $id = $_POST['id'] ?? 0;
        
        $stmt = $conn->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
        break;
}
?>