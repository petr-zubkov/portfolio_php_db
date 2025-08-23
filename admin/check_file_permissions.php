<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка прав доступа к файлам</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f8ff; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Проверка прав доступа к файлам</h1>';

$files_to_check = [
    'save_settings.php',
    'save_settings_working.php',
    'save_settings_final.php'
];

echo '<table>';
echo '<tr><th>Файл</th><th>Существует</th><th>Читаемый</th><th>Записываемый</th><th>Размер</th><th>Права</th></tr>';

foreach ($files_to_check as $file) {
    $filepath = __DIR__ . '/' . $file;
    
    echo '<tr>';
    echo '<td>' . $file . '</td>';
    
    if (file_exists($filepath)) {
        echo '<td class="success">✓</td>';
        echo '<td class="success">' . (is_readable($filepath) ? '✓' : '✗') . '</td>';
        echo '<td class="success">' . (is_writable($filepath) ? '✓' : '✗') . '</td>';
        echo '<td>' . filesize($filepath) . ' байт</td>';
        echo '<td>' . substr(sprintf('%o', fileperms($filepath)), -4) . '</td>';
    } else {
        echo '<td class="error">✗</td>';
        echo '<td>-</td>';
        echo '<td>-</td>';
        echo '<td>-</td>';
        echo '<td>-</td>';
    }
    
    echo '</tr>';
}

echo '</table>';

// Проверяем содержимое save_settings.php
$save_settings_file = __DIR__ . '/save_settings.php';
if (file_exists($save_settings_file)) {
    echo '<div class="info">';
    echo '<h3>Содержимое save_settings.php:</h3>';
    echo '<pre>' . htmlspecialchars(file_get_contents($save_settings_file)) . '</pre>';
    echo '</div>';
} else {
    echo '<div class="error">Файл save_settings.php не существует</div>';
}

echo '<br><a href="replace_save_settings.php">Заменить файл</a>';
echo '<br><a href="test_final.php">Вернуться к тесту</a>';
echo '</body></html>';
?>