<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

// Код рабочей админ-панели
$working_code = file_get_contents(__DIR__ . '/final_index.php');

// Записываем в index.php
$result = file_put_contents(__DIR__ . '/index.php', $working_code);

if ($result !== false) {
    echo '<div style="color: green; padding: 20px;">✓ Админ-панель успешно заменена</div>';
} else {
    echo '<div style="color: red; padding: 20px;">✗ Ошибка при замене файла</div>';
}

echo '<br><a href="index.php">Открыть админ-панель</a>';
?>