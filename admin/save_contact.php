<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 1;
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$telegram = $_POST['telegram'] ?? '';

$stmt = $conn->prepare("UPDATE contact SET email = ?, phone = ?, telegram = ? WHERE id = ?");
$stmt->bind_param("sssi", $email, $phone, $telegram, $id);
$stmt->execute();

echo json_encode(['success' => true]);
?>