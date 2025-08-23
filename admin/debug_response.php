<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Тест ответа save_settings.php</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .request-info { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .response { background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffe8e8; color: red; }
        .success { background: #e8ffe8; color: green; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Тест ответа save_settings.php</h1>
    
    <div class="request-info">
        <h3>Тестовый запрос</h3>
        <p>Отправляем POST запрос с тестовыми данными...</p>
    </div>';

// Тестовые данные
$testData = [
    'id' => 1,
    'site_title' => 'Тестовый сайт ' . date('H:i:s'),
    'hero_title' => 'Тестовый герой',
    'hero_subtitle' => 'Тестовый подзаголовок',
    'avatar' => 'assets/img/placeholder.jpg',
    'about_text' => 'Текст о себе',
    'primary_color' => '#2c3e50',
    'secondary_color' => '#3498db',
    'accent_color' => '#e74c3c',
    'text_color' => '#333333',
    'bg_color' => '#ffffff',
    'font_family' => 'Roboto',
    'bg_image' => '',
    'experience_years' => 5,
    'projects_count' => 100,
    'clients_count' => 50
];

echo '<div class="request-info">';
echo '<h4>Отправляемые данные:</h4>';
echo '<pre>' . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
echo '</div>';

// Создаем контекст запроса
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                   "Cookie: " . session_name() . "=" . session_id() . "\r\n",
        'content' => http_build_query($testData)
    ]
];

$context = stream_context_create($options);
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/save_settings.php';

echo '<div class="request-info">';
echo '<h4>URL запроса:</h4>';
echo '<code>' . htmlspecialchars($url) . '</code>';
echo '</div>';

// Выполняем запрос
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo '<div class="response error">';
    echo '<h3>Ошибка выполнения запроса</h3>';
    echo '<p>Ошибка: ' . error_get_last()['message'] . '</p>';
    echo '</div>';
} else {
    echo '<div class="response">';
    echo '<h3>Ответ сервера</h3>';
    echo '<h4>HTTP заголовки:</h4>';
    echo '<pre>' . $http_response_header[0] . '</pre>';
    
    echo '<h4>Тело ответа:</h4>';
    echo '<pre>' . htmlspecialchars($response) . '</pre>';
    
    // Пытаемся декодировать JSON
    $jsonData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo '<div class="' . ($jsonData['success'] ? 'success' : 'error') . '">';
        echo '<h4>Результат:</h4>';
        echo '<p>Статус: ' . ($jsonData['success'] ? 'Успех' : 'Ошибка') . '</p>';
        echo '<p>Сообщение: ' . htmlspecialchars($jsonData['message']) . '</p>';
        if (isset($jsonData['affected_rows'])) {
            echo '<p>Затронуто строк: ' . $jsonData['affected_rows'] . '</p>';
        }
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h4>Ошибка декодирования JSON:</h4>';
        echo '<p>' . json_last_error_msg() . '</p>';
        echo '</div>';
    }
    echo '</div>';
}

// Проверяем лог-файл
$logFile = __DIR__ . '/error_log.txt';
if (file_exists($logFile)) {
    echo '<div class="request-info">';
    echo '<h3>Последние записи в лог-файле:</h3>';
    echo '<pre>';
    $logs = array_slice(file($logFile), -10); // Последние 10 строк
    foreach ($logs as $log) {
        echo htmlspecialchars($log);
    }
    echo '</pre>';
    echo '<p><a href="clear_log.php">Очистить лог</a></p>';
    echo '</div>';
} else {
    echo '<div class="request-info">';
    echo '<p>Лог-файл не найден</p>';
    echo '</div>';
}

echo '<p><a href="index.php">Вернуться в админ-панель</a></p>';
echo '</body></html>';
?>