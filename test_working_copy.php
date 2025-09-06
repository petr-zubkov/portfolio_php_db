<?php
// Тест нового обработчика send_message_working_copy.php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Тест send_message_working_copy.php</h1>";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📋 Что делаем:</h3>";
echo "<ul>";
echo "<li>Тестируем новый обработчик send_message_working_copy.php</li>";
echo "<li>Используем те же данные, что и в форме</li>";
echo "<li>Проверяем, приходит ли письмо</li>";
echo "</ul>";
echo "</div>";

// Тестовые данные
$test_data = [
    'name' => 'Тест с рабочего обработчика',
    'email' => 'test@working-copy.com',
    'message' => 'Это тестовое сообщение через send_message_working_copy.php'
];

echo "<div style='background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📤 Тестовые данные:</h3>";
echo "<div><strong>Имя:</strong> " . htmlspecialchars($test_data['name']) . "</div>";
echo "<div><strong>Email:</strong> " . htmlspecialchars($test_data['email']) . "</div>";
echo "<div><strong>Сообщение:</strong> " . htmlspecialchars($test_data['message']) . "</div>";
echo "</div>";

// Проверяем настройки
require_once 'config.php';

echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>⚙️ Настройки SMTP:</h3>";
echo "<div><strong>Host:</strong> " . SMTP_HOST . "</div>";
echo "<div><strong>Port:</strong> " . SMTP_PORT . "</div>";
echo "<div><strong>Username:</strong> " . SMTP_USERNAME . "</div>";
echo "<div><strong>Password:</strong> " . (SMTP_PASSWORD === 'your_password_here' ? '❌ Не настроен' : '✅ Настроен') . "</div>";
echo "<div><strong>To:</strong> " . SMTP_TO_EMAIL . "</div>";
echo "</div>";

// Тестируем через POST запрос
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🧪 Тест POST запроса к send_message_working_copy.php:</h3>";

// Создаем контекст для POST запроса
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($test_data)
    ]
];

$context = stream_context_create($options);

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
echo "<strong>Отправляю POST запрос...</strong><br>";
echo "<small>URL: https://zubkov.space/send_message_working_copy.php</small><br>";
echo "<small>Data: " . http_build_query($test_data) . "</small>";
echo "</div>";

$response = file_get_contents('https://zubkov.space/send_message_working_copy.php', false, $context);

if ($response === false) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>❌ Ошибка POST запроса!</h4>";
    echo "<p>Не удалось отправить POST запрос к send_message_working_copy.php</p>";
    echo "<p>Проверьте, существует ли файл</p>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h4>✅ POST запрос отправлен!</h4>";
    echo "<p>Ответ сервера:</p>";
    echo "<pre style='background: white; padding: 10px; border-radius: 3px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($response) . "</pre>";
    echo "</div>";
    
    // Парсим JSON ответ
    $json_data = json_decode($response, true);
    if ($json_data) {
        echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin-top: 15px;'>";
        echo "<h4>📊 Анализ ответа:</h4>";
        echo "<div><strong>Success:</strong> " . ($json_data['success'] ? '✅ Да' : '❌ Нет') . "</div>";
        echo "<div><strong>Message:</strong> " . htmlspecialchars($json_data['message']) . "</div>";
        if (isset($json_data['debug'])) {
            echo "<div><strong>Debug:</strong><br>";
            echo "<pre style='background: white; padding: 10px; border-radius: 3px; font-size: 12px;'>" . json_encode($json_data['debug'], JSON_PRETTY_PRINT) . "</pre>";
            echo "</div>";
        }
        echo "</div>";
    }
}

echo "</div>";

// Проверяем файл
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Проверка файла send_message_working_copy.php:</h3>";

if (file_exists('send_message_working_copy.php')) {
    echo "<div style='color: green;'>✅ Файл существует</div>";
    
    // Проверяем размер файла
    $size = filesize('send_message_working_copy.php');
    echo "<div>Размер файла: " . round($size / 1024, 2) . " KB</div>";
    
    // Проверяем содержимое
    $content = file_get_contents('send_message_working_copy.php');
    if (strpos($content, 'sendWorkingSMTP') !== false) {
        echo "<div style='color: green;'>✅ Функция sendWorkingSMTP найдена</div>";
    } else {
        echo "<div style='color: red;'>❌ Функция sendWorkingSMTP не найдена</div>";
    }
    
    if (strpos($content, 'ssl://smtp.mail.ru:465') !== false) {
        echo "<div style='color: green;'>✅ Настройки SMTP найдены</div>";
    } else {
        echo "<div style='color: red;'>❌ Настройки SMTP не найдены</div>";
    }
    
} else {
    echo "<div style='color: red;'>❌ Файл send_message_working_copy.php не существует</div>";
}

echo "</div>";

// Проверяем текущий обработчик в script.js
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Текущий обработчик в script.js:</h3>";

if (file_exists('assets/js/script.js')) {
    $content = file_get_contents('assets/js/script.js');
    
    if (preg_match("/fetch\('([^']+)'/", $content, $matches)) {
        $current_handler = $matches[1];
        echo "<div style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;'>";
        echo "<strong>Текущий обработчик:</strong> <code>$current_handler</code><br>";
        
        if ($current_handler === 'send_message_working_copy.php') {
            echo "<p style='color: green;'>✅ Форма использует правильный обработчик!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Форма использует $current_handler, нужно send_message_working_copy.php</p>";
        }
        echo "</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Файл script.js не найден</div>";
}

echo "</div>";

// Инструкции
echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>💡 Что делать дальше:</h3>";
echo "<ol>";
echo "<li><strong>Проверьте результат POST запроса выше</strong> - если ответ содержит success: true, то обработчик работает</li>";
echo "<li><strong>Проверьте почту</strong> - должно прийти тестовое письмо</li>";
echo "<li><strong>Если всё хорошо</strong> - протестируйте форму на сайте</li>";
echo "<li><strong>Если есть проблемы</strong> - проверьте, что форма использует правильный обработчик</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";
echo "<a href='final_fix.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🚀 Финальное исправление</a>";
echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
echo "</div>";
?>