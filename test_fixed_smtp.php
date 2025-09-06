<?php
// Тест исправленного SMTP обработчика
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Тест send_message_fixed_smtp.php</h1>";

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>✅ Что исправлено:</h3>";
echo "<ul>";
echo "<li>Убран двойной require_once config.php</li>";
echo "<li>Настройки SMTP передаются как параметры</li>";
echo "<li>Исправлена обработка multiline ответов EHLO</li>";
echo "<li>Добавлена детальная отладка</li>";
echo "</ul>";
echo "</div>";

// Тестовые данные
$test_data = [
    'name' => 'Тест с исправленного обработчика',
    'email' => 'test@fixed-smtp.com',
    'message' => 'Это тестовое сообщение через исправленный send_message_fixed_smtp.php'
];

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
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
echo "<h3>🧪 Тест POST запроса к send_message_fixed_smtp.php:</h3>";

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
echo "<small>URL: https://zubkov.space/send_message_fixed_smtp.php</small>";
echo "</div>";

$response = file_get_contents('https://zubkov.space/send_message_fixed_smtp.php', false, $context);

if ($response === false) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>❌ Ошибка POST запроса!</h4>";
    echo "<p>Не удалось отправить POST запрос к send_message_fixed_smtp.php</p>";
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
            echo "<div><strong>Method:</strong> " . htmlspecialchars($json_data['debug']['method']) . "</div>";
            echo "<div><strong>SMTP Sent:</strong> " . ($json_data['debug']['smtp_sent'] ? '✅ Да' : '❌ Нет') . "</div>";
            echo "<div><strong>DB Saved:</strong> " . ($json_data['debug']['db_saved'] ? '✅ Да' : '❌ Нет') . "</div>";
            if (isset($json_data['debug']['note'])) {
                echo "<div><strong>Note:</strong> " . htmlspecialchars($json_data['debug']['note']) . "</div>";
            }
        }
        echo "</div>";
        
        if ($json_data['success'] && isset($json_data['debug']['smtp_sent']) && $json_data['debug']['smtp_sent']) {
            echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px; text-align: center;'>";
            echo "<h2>🎉 УСПЕХ!</h2>";
            echo "<p>SMTP работает! Письмо должно быть отправлено на " . SMTP_TO_EMAIL . "</p>";
            echo "<p>Теперь можно заменить обработчик формы на этот!</p>";
            echo "</div>";
        }
    }
}

echo "</div>";

// Проверяем файл
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Проверка файла send_message_fixed_smtp.php:</h3>";

if (file_exists('send_message_fixed_smtp.php')) {
    echo "<div style='color: green;'>✅ Файл существует</div>";
    
    // Проверяем размер файла
    $size = filesize('send_message_fixed_smtp.php');
    echo "<div>Размер файла: " . round($size / 1024, 2) . " KB</div>";
    
    // Проверяем содержимое
    $content = file_get_contents('send_message_fixed_smtp.php');
    if (strpos($content, 'sendFixedSMTP') !== false) {
        echo "<div style='color: green;'>✅ Функция sendFixedSMTP найдена</div>";
    } else {
        echo "<div style='color: red;'>❌ Функция sendFixedSMTP не найдена</div>";
    }
    
    if (strpos($content, 'ssl://smtp.mail.ru:465') !== false) {
        echo "<div style='color: green;'>✅ Настройки SMTP найдены</div>";
    } else {
        echo "<div style='color: red;'>❌ Настройки SMTP не найдены</div>";
    }
    
} else {
    echo "<div style='color: red;'>❌ Файл send_message_fixed_smtp.php не существует</div>";
}

echo "</div>";

// Форма для быстрого теста
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📧 Быстрый тест формы:</h3>";
echo "<form id='quickTestForm' style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<input type='text' name='name' placeholder='Ваше имя' value='Быстрый тест' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<input type='email' name='email' placeholder='Ваш email' value='quick@test.com' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<textarea name='message' placeholder='Ваше сообщение' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 80px;'>Быстрый тест исправленного обработчика</textarea>";
echo "</div>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>📧 Отправить тест</button>";
echo "</form>";
echo "<div id='quickTestResult' style='margin-top: 15px;'></div>";
echo "</div>";

echo "<script>
document.getElementById('quickTestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('quickTestResult');
    
    resultDiv.innerHTML = '<div style=\"background: #fff3cd; padding: 15px; border-radius: 5px;\">📤 Отправка...</div>';
    
    fetch('send_message_fixed_smtp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            name: formData.get('name'),
            email: formData.get('email'),
            message: formData.get('message')
        })
    })
    .then(response => response.json())
    .then(result => {
        const color = result.success ? 'd4edda' : 'f8d7da';
        const icon = result.success ? '✅' : '❌';
        resultDiv.innerHTML = '<div style=\"background: #' + color + '; padding: 15px; border-radius: 5px;\">' + icon + ' ' + result.message + '</div>';
        
        if (result.debug && result.debug.smtp_sent) {
            resultDiv.innerHTML += '<div style=\"background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 10px;\">🎉 SMTP работает! Письмо отправлено!</div>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div style=\"background: #f8d7da; padding: 15px; border-radius: 5px;\">❌ Ошибка сети: ' + error.message + '</div>';
    });
});
</script>";

// Инструкции
echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>💡 Что делать дальше:</h3>";
echo "<ol>";
echo "<li><strong>Протестируйте форму выше</strong> - отправьте быстрый тест</li>";
echo "<li><strong>Если SMTP работает</strong> (smtp_sent: true) - замените обработчик формы</li>";
echo "<li><strong>Замените в script.js:</strong> send_message_working_copy.php на send_message_fixed_smtp.php</li>";
echo "<li><strong>Протестируйте форму на сайте</strong></li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";
echo "<a href='final_fix.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🚀 Финальное исправление</a>";
echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
echo "</div>";
?>