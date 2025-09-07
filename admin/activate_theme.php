<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_themes.php');
    exit;
}

$theme_id = $_GET['id'];

// Деактивируем все темы
$conn->query("UPDATE themes SET is_active = 0");

// Активируем выбранную тему
$stmt = $conn->prepare("UPDATE themes SET is_active = 1 WHERE id = ?");
$stmt->bind_param("i", $theme_id);
$stmt->execute();

$_SESSION['success'] = "Тема успешно активирована!";
header("Location: manage_themes.php");
exit;
?>