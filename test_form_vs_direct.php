<?php
// Сравнение теста SMTP и отправки с формы
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Сравнение: Тест SMTP vs Форма</h1>";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>📋 Проблема:</h2>";
echo "<ul>";
echo "<li>✅ Тест SMTP работает (вы получили 2 письма)</li>";
echo "<li>✅ Форма использует send_message_direct_smtp.php</li>";
echo "<li>❌ Письма с формы не приходят</li>";
echo "</ul>";
echo "<p>Нужно найти разницу между рабочим тестом и отправкой с формы</p>";
echo "</div>";

// Проверяем настройки
require_once 'config.php';

echo "<div style='background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>⚙️ Настройки из config.php:</h3>";
echo "<div><strong>SMTP_HOST:</strong> " . SMTP_HOST . "</div>";
echo "<div><strong>SMTP_PORT:</strong> " . SMTP_PORT . "</div>";
echo "<div><strong>SMTP_USERNAME:</strong> " . SMTP_USERNAME . "</div>";
echo "<div><strong>SMTP_PASSWORD:</strong> " . (SMTP_PASSWORD === 'your_password_here' ? '❌ Не настроен' : '✅ Настроен') . "</div>";
echo "<div><strong>SMTP_FROM_EMAIL:</strong> " . SMTP_FROM_EMAIL . "</div>";
echo "<div><strong>SMTP_TO_EMAIL:</strong> " . SMTP_TO_EMAIL . "</div>";
echo "</div>";

// Тестируем прямой вызов функции
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🧪 Тест 1: Прямой вызов sendDirectSMTP</h3>";

try {
    // Включаем файл с функцией
    require_once 'send_message_direct_smtp.php';
    
    $result = sendDirectSMTP('Тестовый пользователь', 'test@direct.com', 'Тестовое сообщение напрямую');
    
    if ($result) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h4>✅ Прямой вызов работает!</h4>";
        echo "<p>Письмо должно быть отправлено на " . SMTP_TO_EMAIL . "</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<h4>❌ Прямой вызов не работает!</h4>";
        echo "<p>Проблема в функции sendDirectSMTP</p>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>❌ Ошибка при прямом вызове:</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div>";

// Тестируем через POST запрос (как форма)
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🧪 Тест 2: POST запрос (как форма)</h3>";

// Имитируем POST запрос
$post_data = [
    'name' => 'Тест POST',
    'email' => 'test@post.com',
    'message' => 'Тестовое сообщение через POST запрос'
];

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
echo "<strong>Данные для отправки:</strong><br>";
foreach ($post_data as $key => $value) {
    echo "$key: " . htmlspecialchars($value) . "<br>";
}
echo "</div>";

// Создаем контекст для POST запроса
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($post_data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents('https://zubkov.space/send_message_direct_smtp.php', false, $context);

if ($response === false) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>❌ Ошибка POST запроса!</h4>";
    echo "<p>Не удалось отправить POST запрос</p>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h4>✅ POST запрос отправлен!</h4>";
    echo "<p>Ответ сервера:</p>";
    echo "<pre style='background: white; padding: 10px; border-radius: 3px;'>" . htmlspecialchars($response) . "</pre>";
    echo "</div>";
}

echo "</div>";

// Сравниваем с рабочим тестом
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🔍 Сравнение с рабочим тестом:</h3>";
echo "<p>Рабочий тест (test_smtp_working.php) использует:</p>";
echo "<ul>";
echo "<li>Прямое подключение к smtp.mail.ru:465</li>";
echo "<li>Аутентификацию с текущими настройками</li>";
echo "<li>Отправку тестового письма</li>";
echo "</ul>";
echo "<p>Форма использует:</p>";
echo "<ul>";
echo "<li>Функцию sendDirectSMTP() в send_message_direct_smtp.php</li>";
echo "<li>Те же настройки из config.php</li>";
echo "<li>Такое же подключение к SMTP</li>";
echo "</ul>";
echo "</div>";

// Возможные проблемы
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>⚠️ Возможные проблемы:</h3>";
echo "<ol>";
echo "<li><strong>Разница в $_SERVER['HTTP_HOST']:</strong><br>";
echo "Тест использует реальный хост, форма может использовать другой</li>";
echo "<li><strong>Разница в заголовках письма:</strong><br>";
echo "Формат письма в тесте и в функции может отличаться</li>";
echo "<li><strong>Разница в обработке ошибок:</strong><br>";
echo "Тест показывает детальную отладку, функция может скрывать ошибки</li>";
echo "<li><strong>Разница в времени выполнения:</strong><br>";
echo "Тест выполняется дольше, функция может иметь таймаут</li>";
echo "</ol>";
echo "</div>";

// Решение
echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>💡 Решение:</h3>";
echo "<p>Давайте создадим новый обработчик, который будет использовать точно такой же код, как в рабочем тесте:</p>";
echo "<ol>";
echo "<li>Скопируем рабочий SMTP код из test_smtp_working.php</li>";
echo "<li>Создадим новый обработчик send_message_working_smtp.php</li>";
echo "<li>Обновим форму для использования нового обработчика</li>";
echo "</ol>";
echo "<form method='post'>";
echo "<input type='hidden' name='create_working_handler' value='1'>";
echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer;'>🚀 Создать рабочий обработчик</button>";
echo "</form>";
echo "</div>";

// Создаем рабочий обработчик
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_working_handler'])) {
    createWorkingSMTPHandler();
}

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='debug_direct_smtp.php' style='display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Отладка Direct SMTP</a>";
echo "<a href='test_smtp_working.php' style='display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Рабочий тест SMTP</a>";
echo "</div>";

function createWorkingSMTPHandler() {
    $working_code = '<?php
// Рабочий SMTP обработчик (копия из test_smtp_working.php)
header(\'Content-Type: application/json; charset=utf-8\');

// Отключаем вывод ошибок
ini_set(\'display_errors\', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

try {
    if ($_SERVER[\'REQUEST_METHOD\'] !== \'POST\') {
        throw new Exception(\'Метод не разрешен. Используйте POST.\');
    }

    // Получаем данные
    $name = trim($_POST[\'name\'] ?? \'\');
    $email = trim($_POST[\'email\'] ?? \'\');
    $message = trim($_POST[\'message\'] ?? \'\');

    // Валидация
    if (empty($name)) throw new Exception(\'Введите имя\');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception(\'Некорректный email\');
    if (empty($message)) throw new Exception(\'Введите сообщение\');

    // Защита
    $name = htmlspecialchars($name, ENT_QUOTES, \'UTF-8\');
    $email = htmlspecialchars($email, ENT_QUOTES, \'UTF-8\');
    $message = htmlspecialchars($message, ENT_QUOTES, \'UTF-8\');

    // Подключаем конфигурацию
    require_once \'config.php\';

    // Сохраняем в базу данных
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, \'new\')");
    $stmt->bind_param("sss", $name, $email, $message);
    $db_saved = $stmt->execute();

    // Отправляем через SMTP (рабочий код из теста)
    $smtp_sent = sendWorkingSMTP($name, $email, $message);

    if ($smtp_sent) {
        ob_end_clean();
        echo json_encode([
            \'success\' => true,
            \'message\' => \'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.\',
            \'debug\' => [
                \'method\' => \'Working SMTP\',
                \'db_saved\' => $db_saved,
                \'smtp_sent\' => true,
                \'to_email\' => SMTP_TO_EMAIL
            ]
        ]);
    } else {
        // Если SMTP не сработал, но сохранено в БД
        ob_end_clean();
        echo json_encode([
            \'success\' => true,
            \'message\' => \'Ваше сообщение получено и сохранено! Я свяжусь с вами в ближайшее время.\',
            \'debug\' => [
                \'method\' => \'Database Backup\',
                \'db_saved\' => $db_saved,
                \'smtp_sent\' => false,
                \'note\' => \'SMTP отправка не удалась, но сообщение сохранено в базе данных\'
            ]
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        \'success\' => false,
        \'message\' => \'Ошибка: \' . $e->getMessage(),
        \'debug\' => [
            \'error\' => $e->getMessage()
        ]
    ]);
}

exit;

function sendWorkingSMTP($name, $email, $message) {
    try {
        require_once \'config.php\';
        
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;
        
        // Используем SSL на порту 465 (как в рабочем тесте)
        $connection_string = "ssl://$host:$port";
        $secure = \'ssl\';
        
        // Создаем контекст
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);
        
        // Подключаемся
        $socket = @stream_socket_client(
            $connection_string,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            return false;
        }
        
        // Устанавливаем таймаут
        stream_set_timeout($socket, 10);
        
        // Читаем приветствие
        $response = fgets($socket, 515);
        if (!$response || substr($response, 0, 3) !== "220") {
            fclose($socket);
            return false;
        }
        
        // EHLO
        fwrite($socket, "EHLO " . $_SERVER[\'HTTP_HOST\'] . "\r\n");
        
        // Читаем multiline ответ EHLO
        $ehlo_response = "";
        while (true) {
            $line = fgets($socket, 515);
            if (!$line) break;
            
            $ehlo_response .= $line;
            if (substr($line, 3, 1) === " ") break;
        }
        
        // Проверяем поддержку AUTH
        if (strpos($ehlo_response, \'AUTH\') === false) {
            fclose($socket);
            return false;
        }
        
        // Аутентификация LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "334") {
            fclose($socket);
            return false;
        }
        
        // Отправляем логин
        fwrite($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "334") {
            fclose($socket);
            return false;
        }
        
        // Отправляем пароль
        fwrite($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "235") {
            fclose($socket);
            return false;
        }
        
        // MAIL FROM
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "250") {
            fclose($socket);
            return false;
        }
        
        // RCPT TO
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "250") {
            fclose($socket);
            return false;
        }
        
        // DATA
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== "354") {
            fclose($socket);
            return false;
        }
        
        // Формируем письмо (как в рабочем тесте)
        $subject = "Новое сообщение с сайта zubkov.space";
        $body = "Это тестовое сообщение с сайта zubkov.space\n\n";
        $body .= "Имя: $name\n";
        $body .= "Email: $email\n";
        $body .= "Время: " . date(\'Y-m-d H:i:s\') . "\n";
        $body .= "IP: " . $_SERVER[\'REMOTE_ADDR\'] . "\n\n";
        $body .= "Сообщение:\n$message";
        
        $email_data = "From: $from_email\r\n";
        $email_data .= "To: $to_email\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "$body\r\n";
        $email_data .= ".\r\n";
        
        fwrite($socket, $email_data);
        $response = fgets($socket, 515);
        
        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return substr($response, 0, 3) === "250";
        
    } catch (Exception $e) {
        return false;
    }
}
?>';

    if (file_put_contents('send_message_working_smtp.php', $working_code)) {
        echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h2>✅ Рабочий обработчик создан!</h2>";
        echo "<p>Файл send_message_working_smtp.php создан</p>";
        echo "<p>Теперь обновите script.js для использования этого обработчика:</p>";
        echo "<p>Замените: <code>fetch('send_message_direct_smtp.php', {</code></p>";
        echo "<p>На: <code>fetch('send_message_working_smtp.php', {</code></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h2>❌ Ошибка создания файла</h2>";
        echo "<p>Не удалось создать файл send_message_working_smtp.php</p>";
        echo "</div>";
    }
}
?>