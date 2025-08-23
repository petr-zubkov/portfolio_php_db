<?php
echo '<!DOCTYPE html>
<html>
<head>
    <title>Создание файлов админ-панели</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: $721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
    </style>
</head>
<body>
    <h1>Создание файлов админ-панели</h1>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $files_to_create = [
        'index.php' => 'final_index.php',
        'auth.php' => 'auth.php',
        'logout.php' => 'logout.php'
    ];
    
    foreach ($files_to_create as $target => $source) {
        $source_path = __DIR__ . '/' . $source;
        $target_path = __DIR__ . '/' . $target;
        
        if (file_exists($source_path)) {
            if (copy($source_path, $target_path)) {
                echo '<div class="success">✓ Файл ' . $target . ' создан</div>';
            } else {
                echo '<div class="error">✗ Ошибка при создании файла ' . $target . '</div>';
            }
        } else {
            echo '<div class="error">✗ Исходный файл ' . $source . ' не найден</div>';
        }
    }
    
    echo '<div class="success">✓ Все файлы созданы!</div>';
    echo '<script>
        setTimeout(function() {
            window.location.href = "restore_access.php";
        }, 2000);
    </script>';
    
} else {
    echo '<div class="info">';
    echo '<p>Эта страница создаст необходимые файлы админ-панели.</p>';
    echo '<p>Будут созданы файлы:</p>';
    echo '<ul>';
    echo '<li>index.php - основная страница админ-панели</li>';
    echo '<li>auth.php - страница входа</li>';
    echo '<li>logout.php - страница выхода</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<form method="post">';
    echo '<button type="submit">Создать файлы админ-панели</button>';
    echo '</form>';
}

echo '<br><a href="check_session.php">Вернуться к проверке сессии</a>';
echo '</body></html>';
?>