<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

$logType = $_GET['type'] ?? 'settings';
$logFile = __DIR__ . '/logs/' . $logType . '.log';

if (file_exists($logFile)) {
    if (unlink($logFile)) {
        $_SESSION['message'] = 'Логи успешно очищены';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при очистке логов';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Лог-файл не найден';
    $_SESSION['message_type'] = 'warning';
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'view_logs.php?type=' . $logType;
header('Location: ' . $referer);
exit;
?>