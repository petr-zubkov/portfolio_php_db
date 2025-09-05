<?php
// Улучшенный тест SMTP с лучшей обработкой ответов сервера
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Улучшенный тест SMTP</h1>";

require_once 'config.php';

echo "<div style='background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h3>Текущие настройки:</h3>";
echo "<strong>SMTP Host:</strong> " . SMTP_HOST . "<br>";
echo "<strong>SMTP Port:</strong> " . SMTP_PORT . "<br>";
echo "<strong>SMTP Username:</strong> " . SMTP_USERNAME . "<br>";
echo "<strong>SMTP Password:</strong> " . (SMTP_PASSWORD === 'your_password_here' ? '❌ Не настроен' : '✅ Настроен') . "<br>";
echo "<strong>From Email:</strong> " . SMTP_FROM_EMAIL . "<br>";
echo "<strong>To Email:</strong> " . SMTP_TO_EMAIL . "<br>";
echo "</div>";

if (SMTP_PASSWORD === 'your_password_here') {
    echo "<div style='color: red; font-weight: bold; padding: 15px; background: #ffe6e6; border-radius: 5px;'>";
    echo "❌ Пароль SMTP не настроен!<br>";
    echo "Пожалуйста, откройте config.php и замените 'your_password_here' на ваш реальный пароль.";
    echo "</div>";
    exit;
}

// Функция чтения всех строк ответа SMTP
function readAllResponses($socket) {
    $responses = [];
    while ($line = fgets($socket, 515)) {
        $line = trim($line);
        if (empty($line)) break;
        $responses[] = $line;
        // Если это последняя строка ответа (не начинается с кода)
        if (substr($line, 3, 1) !== ' ') break;
    }
    return $responses;
}

// Функция тестирования SMTP с улучшенной обработкой
function testSMTPImproved($host, $port, $username, $password, $from_email, $to_email) {
    try {
        echo "<div style='margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
        echo "<h4>Тестирование: $host:$port</h4>";

        // Создаем контекст для SSL
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);

        // Подключаемся к SMTP серверу
        $socket = @stream_socket_client(
            $host . ":" . $port,
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            echo "<div style='color: red;'>❌ Не удалось подключиться: $errstr</div></div>";
            return false;
        }

        echo "<div style='color: green;'>✅ Соединение установлено</div>";

        // Читаем приветствие сервера
        $greeting = fgets($socket, 515);
        echo "<div>Приветствие: " . htmlspecialchars(trim($greeting)) . "</div>";

        // Отправляем EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $ehlo_responses = readAllResponses($socket);
        echo "<div>EHLO ответы:<br>";
        foreach ($ehlo_responses as $response) {
            echo "• " . htmlspecialchars($response) . "<br>";
        }
        echo "</div>";

        // Проверяем наличие AUTH в ответах
        $has_auth = false;
        foreach ($ehlo_responses as $response) {
            if (strpos(strtoupper($response), 'AUTH') !== false) {
                $has_auth = true;
                break;
            }
        }

        if (!$has_auth) {
            echo "<div style='color: orange;'>⚠️ Сервер не явно указал поддержку AUTH, пробуем anyway</div>";
        }

        // Пробуем AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $auth_response = fgets($socket, 515);
        echo "<div>AUTH LOGIN: " . htmlspecialchars(trim($auth_response)) . "</div>";

        if (substr($auth_response, 0, 3) !== "334") {
            echo "<div style='color: red;'>❌ Сервер не готов принять логин</div></div>";
            fclose($socket);
            return false;
        }

        // Отправляем логин (username)
        fwrite($socket, base64_encode($username) . "\r\n");
        $user_response = fgets($socket, 515);
        echo "<div>Username response: " . htmlspecialchars(trim($user_response)) . "</div>";

        if (substr($user_response, 0, 3) !== "334") {
            echo "<div style='color: red;'>❌ Сервер не готов принять пароль</div></div>";
            fclose($socket);
            return false;
        }

        // Отправляем пароль
        fwrite($socket, base64_encode($password) . "\r\n");
        $pass_response = fgets($socket, 515);
        echo "<div>Password response: " . htmlspecialchars(trim($pass_response)) . "</div>";

        if (substr($pass_response, 0, 3) === "235") {
            echo "<div style='color: green;'>✅ Аутентификация успешна!</div>";

            // Отправляем тестовое письмо
            fwrite($socket, "MAIL FROM:<" . $from_email . ">\r\n");
            $mail_response = fgets($socket, 515);
            echo "<div>MAIL FROM: " . htmlspecialchars(trim($mail_response)) . "</div>";

            fwrite($socket, "RCPT TO:<" . $to_email . ">\r\n");
            $rcpt_response = fgets($socket, 515);
            echo "<div>RCPT TO: " . htmlspecialchars(trim($rcpt_response)) . "</div>";

            fwrite($socket, "DATA\r\n");
            $data_response = fgets($socket, 515);
            echo "<div>DATA: " . htmlspecialchars(trim($data_response)) . "</div>";

            // Формируем тестовое письмо
            $email_data = "From: " . $from_email . "\r\n";
            $email_data .= "To: " . $to_email . "\r\n";
            $email_data .= "Subject: Тест SMTP zubkov.space\r\n";
            $email_data .= "MIME-Version: 1.0\r\n";
            $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $email_data .= "\r\n";
            $email_data .= "Это тестовое сообщение для проверки работы SMTP.\r\n";
            $email_data .= "Время: " . date('Y-m-d H:i:s') . "\r\n";
            $email_data .= "Сервер: " . $_SERVER['HTTP_HOST'] . "\r\n";
            $email_data .= "\r\n.\r\n";

            fwrite($socket, $email_data);
            $final_response = fgets($socket, 515);
            echo "<div>Финальный ответ: " . htmlspecialchars(trim($final_response)) . "</div>";

            if (substr($final_response, 0, 3) === "250") {
                echo "<div style='color: green; font-weight: bold; font-size: 18px;'>🎉 SMTP работает!</div>";
                echo "<p>✅ Тестовое письмо отправлено на $to_email</p>";
                
                // Закрываем соединение
                fwrite($socket, "QUIT\r\n");
                fclose($socket);
                
                return true;
            } else {
                echo "<div style='color: red;'>❌ Ошибка при отправке письма</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ Ошибка аутентификации</div>";
            echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "<strong>Возможные причины:</strong><br>";
            echo "• Неверный пароль<br>";
            echo "• Нужно включить SMTP в настройках Mail.ru<br>";
            echo "• Требуется пароль для приложений<br>";
            echo "• Двухфакторная аутентификация включена</div>";
        }

        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return false;

    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Исключение: " . $e->getMessage() . "</div></div>";
        return false;
    }
}

// Тестируем разные конфигурации
$tests = [
    [
        'host' => 'smtp.mail.ru',
        'port' => 465,
        'name' => 'Mail.ru SSL (465)'
    ],
    [
        'host' => 'smtp.mail.ru',
        'port' => 587,
        'name' => 'Mail.ru TLS (587)'
    ]
];

$any_success = false;

foreach ($tests as $test) {
    $success = testSMTPImproved(
        $test['host'],
        $test['port'],
        SMTP_USERNAME,
        SMTP_PASSWORD,
        SMTP_FROM_EMAIL,
        SMTP_TO_EMAIL
    );
    
    if ($success) {
        $any_success = true;
        break; // Если один метод работает, прекращаем тестирование
    }
}

if ($any_success) {
    echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: green;'>🎉 SMTP работает!</h3>";
    echo "<p>Теперь можно использовать обработчик <code>send_message_improved.php</code></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: red;'>❌ SMTP не работает</h3>";
    echo "<p>Рекомендуем использовать альтернативные методы отправки:</p>";
    echo "<ul>";
    echo "<li><strong>Telegram уведомления</strong> - самый надежный способ</li>";
    echo "<li><strong>Внешние сервисы</strong> - SendGrid, Mailgun</li>";
    echo "<li><strong>Резервная отправка</strong> - через функцию mail()</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
if ($any_success) {
    echo "<a href='send_message_improved.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>📧 Улучшенный обработчик</a>";
}
echo "<a href='send_message_fallback.php' style='display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🔄 Резервный обработчик</a>";
echo "</div>";

// Инструкции по настройке Mail.ru
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h3>📧 Полная настройка Mail.ru:</h3>";
echo "<ol>";
echo "<li><strong>Включите SMTP:</strong><br>";
echo "Настройки → Все настройки → Почтовые программы → Включите IMAP и SMTP</li>";
echo "<li><strong>Пароль для приложений (если основной не работает):</strong><br>";
echo "Настройки → Безопасность → Пароли для внешних приложений → Создайте новый пароль</li>";
echo "<li><strong>Отключите двухфакторную аутентификацию</strong> (временно для теста)</li>";
echo "<li><strong>Проверьте папку 'Спам'</strong> - первые письма могут туда попадать</li>";
echo "</ol>";
echo "</div>";
?>