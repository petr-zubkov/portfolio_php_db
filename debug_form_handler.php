<?php
// Диагностика обработчика формы
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Диагностика обработчика формы</h1>";

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>✅ Что мы знаем:</h2>";
echo "<ul>";
echo "<li>SMTP работает - тестовые письма приходят</li>";
echo "<li>Форма на сайте не отправляет письма</li>";
echo "<li>Проблема в обработчике формы</li>";
echo "</ul>";
echo "</div>";

// Проверяем текущий обработчик в JavaScript
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📄 Текущий обработчик в script.js:</h3>";

if (file_exists('assets/js/script.js')) {
    $script_content = file_get_contents('assets/js/script.js');
    
    // Ищем строку с fetch
    if (preg_match("/fetch\('([^']+)'/", $script_content, $matches)) {
        $current_handler = $matches[1];
        echo "<div style='background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin: 10px 0;'>";
        echo "<strong>Текущий обработчик:</strong> <code>$current_handler</code><br>";
        
        if ($current_handler === 'send_message_fallback.php') {
            echo "<p style='color: orange;'>⚠️ Используется fallback обработчик (простая mail() функция)</p>";
            echo "<p><strong>Решение:</strong> Замените на <code>send_message_direct_smtp.php</code></p>";
        } elseif ($current_handler === 'send_message_direct_smtp.php') {
            echo "<p style='color: green;'>✅ Используется прямой SMTP обработчик</p>";
        } elseif ($current_handler === 'send_message_smtp_final.php') {
            echo "<p style='color: green;'>✅ Используется финальный SMTP обработчик</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Используется обработчик: $current_handler</p>";
        }
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ Не найден обработчик формы в script.js</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Файл script.js не найден</p>";
}

echo "</div>";

// Проверяем существование обработчиков
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📁 Доступные обработчики:</h3>";

$handlers = [
    'send_message_fallback.php' => 'Резервный (простая mail())',
    'send_message_direct_smtp.php' => 'Прямой SMTP (рекомендуется)',
    'send_message_smtp_final.php' => 'Финальный SMTP (требует PHPMailer)',
    'send_message.php' => 'Базовый обработчик'
];

foreach ($handlers as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? '✅' : '❌';
    $color = $exists ? 'green' : 'red';
    echo "<div style='color: $color;'>$status $file - $description</div>";
}

echo "</div>";

// Тест обработчика
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🧪 Тест обработчика:</h3>";
echo "<p>Давайте протестируем отправку через форму:</p>";
echo "<form id='testForm' style='background: white; padding: 20px; border-radius: 5px; border: 1px solid #ddd;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label>Имя:</label><br>";
echo "<input type='text' name='name' value='Тест' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label>Email:</label><br>";
echo "<input type='email' name='email' value='test@example.com' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label>Сообщение:</label><br>";
echo "<textarea name='message' required style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 100px;'>Тестовое сообщение с формы</textarea>";
echo "</div>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>📧 Отправить тест</button>";
echo "</form>";
echo "<div id='testResult' style='margin-top: 20px;'></div>";
echo "</div>";

// JavaScript для теста
echo "<script>
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('testResult');
    
    resultDiv.innerHTML = '<div style=\"background: #fff3cd; padding: 15px; border-radius: 5px;\">📤 Отправка...</div>';
    
    // Тестируем разные обработчики
    const handlers = [
        'send_message_fallback.php',
        'send_message_direct_smtp.php',
        'send_message_smtp_final.php'
    ];
    
    let currentHandler = 0;
    
    function testNextHandler() {
        if (currentHandler >= handlers.length) {
            resultDiv.innerHTML += '<div style=\"background: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 10px;\">❌ Все обработчики не сработали</div>';
            return;
        }
        
        const handler = handlers[currentHandler];
        resultDiv.innerHTML += '<div style=\"background: #e2e3e5; padding: 10px; border-radius: 5px; margin-top: 10px;\">🔄 Тестирую: ' + handler + '</div>';
        
        fetch(handler, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                name: formData.get('name'),
                email: formData.get('email'),
                message: formData.get('message') + ' (обработчик: ' + handler + ')'
            })
        })
        .then(response => response.json())
        .then(result => {
            const color = result.success ? 'd4edda' : 'f8d7da';
            const icon = result.success ? '✅' : '❌';
            resultDiv.innerHTML += '<div style=\"background: #' + color + '; padding: 15px; border-radius: 5px; margin-top: 10px;\">' + icon + ' ' + handler + ': ' + result.message + '</div>';
            
            if (result.success) {
                resultDiv.innerHTML += '<div style=\"background: #d1ecf1; padding: 15px; border-radius: 5px; margin-top: 10px;\">💡 Этот обработчик работает! Используйте его в script.js</div>';
            } else {
                currentHandler++;
                setTimeout(testNextHandler, 1000);
            }
        })
        .catch(error => {
            resultDiv.innerHTML += '<div style=\"background: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 10px;\">❌ ' + handler + ': Ошибка сети</div>';
            currentHandler++;
            setTimeout(testNextHandler, 1000);
        });
    }
    
    testNextHandler();
});
</script>";

// Инструкции по исправлению
echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🔧 Как исправить:</h3>";
echo "<ol>";
echo "<li>Откройте файл <code>assets/js/script.js</code></li>";
echo "<li>Найдите строку с <code>fetch('send_message_fallback.php', {</code></li>";
echo "<li>Замените <code>send_message_fallback.php</code> на <code>send_message_direct_smtp.php</code></li>";
echo "<li>Сохраните файл</li>";
echo "<li>Протестируйте форму на сайте</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";
echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
echo "</div>";
?>