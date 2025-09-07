<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Получаем активную тему
$theme_result = $conn->query("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
$theme = $theme_result->fetch_assoc();

if (!$theme) {
    $_SESSION['error'] = "Нет активной темы для синхронизации";
    header("Location: manage_themes.php");
    exit;
}

// Обновляем настройки сайта на основе активной темы
$update_query = "UPDATE settings SET 
    primary_color = ?,
    secondary_color = ?,
    accent_color = ?,
    text_color = ?,
    bg_color = ?,
    font_family = ?,
    site_title = COALESCE(?, site_title),
    hero_title = COALESCE(?, hero_title),
    hero_subtitle = COALESCE(?, hero_subtitle),
    avatar = COALESCE(?, avatar),
    about_text = COALESCE(?, about_text),
    bg_image = COALESCE(?, bg_image)
WHERE id = 1";

$stmt = $conn->prepare($update_query);
$stmt->bind_param(
    "ssssssssssss", 
    $theme['primary_color'],
    $theme['secondary_color'],
    $theme['accent_color'],
    $theme['text_color'],
    $theme['bg_color'],
    $theme['font_family'],
    $theme['site_title'],
    $theme['hero_title'],
    $theme['hero_subtitle'],
    $theme['avatar'],
    $theme['about_text'],
    $theme['bg_image']
);

$stmt->execute();

$_SESSION['success'] = "Настройки сайта успешно синхронизированы с активной темой!";
header("Location: manage_themes.php");
exit;
?>