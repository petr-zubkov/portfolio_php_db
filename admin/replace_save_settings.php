<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Замена save_settings.php</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        textarea { width: 100%; height: 400px; font-family: monospace; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 10px; }
        button:hover { background: #0056b3; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Замена файла save_settings.php</h1>
    
    <div class="info">
        Эта страница поможет заменить текущий файл save_settings.php на рабочую версию.
    </div>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetFile = __DIR__ . '/save_settings.php';
    $sourceFile = __DIR__ . '/save_settings_working.php';
    
    if (file_exists($sourceFile)) {
        // Копируем рабочий файл
        if (copy($sourceFile, $targetFile)) {
            echo '<div class="success">✓ Файл save_settings.php успешно заменен</div>';
            
            // Проверяем права доступа
            if (is_readable($targetFile) && is_writable($targetFile)) {
                echo '<div class="success">✓ Права доступа к файлу в порядке</div>';
            } else {
                echo '<div class="error">✗ Проблема с правами доступа к файлу</div>';
            }
            
            // Показываем содержимое нового файла
            echo '<div class="info">';
            echo '<h3>Содержимое нового файла:</h3>';
            echo '<textarea readonly>' . htmlspecialchars(file_get_contents($targetFile)) . '</textarea>';
            echo '</div>';
            
        } else {
            echo '<div class="error">✗ Ошибка при копировании файла</div>';
        }
    } else {
        echo '<div class="error">✗ Исходный файл save_settings_working.php не найден</div>';
    }
} else {
    // Показываем текущее содержимое файла
    $currentFile = __DIR__ . '/save_settings.php';
    $workingFile = __DIR__ . '/save_settings_working.php';
    
    echo '<div class="form-group">';
    echo '<label>Текущее содержимое save_settings.php:</label>';
    if (file_exists($currentFile)) {
        echo '<textarea readonly>' . htmlspecialchars(file_get_contents($currentFile)) . '</textarea>';
    } else {
        echo '<div class="error">Файл save_settings.php не существует</div>';
    }
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<label>Рабочее содержимое (которое будет установлено):</label>';
    if (file_exists($workingFile)) {
        echo '<textarea readonly>' . htmlspecialchars(file_get_contents($workingFile)) . '</textarea>';
    } else {
        echo '<div class="error">Рабочий файл не найден</div>';
    }
    echo '</div>';
    
    echo '<form method="post">';
    echo '<button type="submit">Заменить файл save_settings.php</button>';
    echo '</form>';
}

echo '<br><br>';
echo '<a href="test_final.php">Вернуться к тесту</a>';
echo '<br>';
echo '<a href="index.php">Вернуться в админ-панель</a>';

echo '</body></html>';
?>