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
    $id = $_POST['id'] ?? 1;
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $telegram = $_POST['telegram'] ?? '';
    
    if (empty($email) || empty($phone) || empty($telegram)) {
        throw new Exception('Заполните все поля');
    }
    
    $stmt = $conn->prepare("UPDATE contact SET email = ?, phone = ?, telegram = ? WHERE id = ?");
    $stmt->bind_param("sssi", $email, $phone, $telegram, $id);
    $stmt->execute();
    
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Контакты успешно сохранены']);
    
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

exit;
?>