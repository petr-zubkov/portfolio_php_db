<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 1;
$site_title = $_POST['site_title'] ?? '';
$hero_title = $_POST['hero_title'] ?? '';
$hero_subtitle = $_POST['hero_subtitle'] ?? '';
$avatar = $_POST['avatar'] ?? '';
$about_text = $_POST['about_text'] ?? '';
$primary_color = $_POST['primary_color'] ?? '';
$secondary_color = $_POST['secondary_color'] ?? '';
$accent_color = $_POST['accent_color'] ?? '';
$text_color = $_POST['text_color'] ?? '';
$bg_color = $_POST['bg_color'] ?? '';
$font_family = $_POST['font_family'] ?? '';
$bg_image = $_POST['bg_image'] ?? '';
$experience_years = $_POST['experience_years'] ?? 0;
$projects_count = $_POST['projects_count'] ?? 0;
$clients_count = $_POST['clients_count'] ?? 0;

$stmt = $conn->prepare("UPDATE settings SET site_title = ?, hero_title = ?, hero_subtitle = ?, avatar = ?, about_text = ?, primary_color = ?, secondary_color = ?, accent_color = ?, text_color = ?, bg_color = ?, font_family = ?, bg_image = ?, experience_years = ?, projects_count = ?, clients_count = ? WHERE id = ?");
$stmt->bind_param("sssssssssssiiii", $site_title, $hero_title, $hero_subtitle, $avatar, $about_text, $primary_color, $secondary_color, $accent_color, $text_color, $bg_color, $font_family, $bg_image, $experience_years, $projects_count, $clients_count, $id);
$stmt->execute();

echo json_encode(['success' => true]);
?>