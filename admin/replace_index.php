<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Замена админ-панели</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Замена админ-панели</h1>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceFile = __DIR__ . '/final_index.php';
    $targetFile = __DIR__ . '/index.php';
    
    if (file_exists($sourceFile)) {
        if (copy($sourceFile, $targetFile)) {
            echo '<div class="success">✓ Админ-панель успешно заменена</div>';
            echo '<div class="info">Теперь все кнопки должны работать корректно</div>';
        } else {
            echo '<div class="error">✗ Ошибка при замене файла</div>';
        }
    } else {
        echo '<div class="error">✗ Исходный файл не найден</div>';
    }
} else {
    echo '<div class="info">
        Эта страница заменит текущую админ-панель на рабочую версию, где все кнопки функционируют правильно.
    </div>';
    
    echo '<form method="post">';
    echo '<button type="submit">Заменить админ-панель</button>';
    echo '</form>';
}

echo '<br><a href="final_index.php">Просмотреть рабочую версию</a>';
echo '<br><a href="index.php">Открыть текущую админ-панель</a>';
echo '</body></html>';
?>