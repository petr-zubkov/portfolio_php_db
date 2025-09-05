<?php
// Простой тест SMTP без внешних зависимостей
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Тест SMTP (простая версия)</h1>";

// Подключаем конфигурацию
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

// Функция тестирования SMTP
function testSMTPConnection() {
    try {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;

        echo "<h3>Тестирование соединения с $host:$port...</h3>";

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
            echo "<div style='color: red;'>❌ Не удалось подключиться к SMTP серверу: $errstr</div>";
            return false;
        }

        echo "<div style='color: green;'>✅ Соединение установлено</div>";

        // Читаем приветствие сервера
        $response = fgets($socket, 515);
        echo "<div>Сервер: " . htmlspecialchars(trim($response)) . "</div>";

        // Отправляем EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = fgets($socket, 515);
        echo "<div>EHLO: " . htmlspecialchars(trim($response)) . "</div>";

        // Проверяем поддержку аутентификации
        if (strpos($response, '250-AUTH') !== false || strpos($response, '250 AUTH') !== false) {
            echo "<div style='color: green;'>✅ Аутентификация поддерживается</div>";

            // Отправляем AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            echo "<div>AUTH LOGIN: " . htmlspecialchars(trim($response)) . "</div>";

            // Отправляем логин
            fwrite($socket, base64_encode($username) . "\r\n");
            $response = fgets($socket, 515);
            echo "<div>Username: " . htmlspecialchars(trim($response)) . "</div>";

            // Отправляем пароль
            fwrite($socket, base64_encode($password) . "\r\n");
            $response = fgets($socket, 515);
            echo "<div>Password: " . htmlspecialchars(trim($response)) . "</div>";

            if (substr($response, 0, 3) === "235") {
                echo "<div style='color: green;'>✅ Аутентификация успешна!</div>";

                // Отправляем тестовое письмо
                fwrite($socket, "MAIL FROM:<" . $from_email . ">\r\n");
                $response = fgets($socket, 515);
                echo "<div>MAIL FROM: " . htmlspecialchars(trim($response)) . "</div>";

                fwrite($socket, "RCPT TO:<" . $to_email . ">\r\n");
                $response = fgets($socket, 515);
                echo "<div>RCPT TO: " . htmlspecialchars(trim($response)) . "</div>";

                fwrite($socket, "DATA\r\n");
                $response = fgets($socket, 515);
                echo "<div>DATA: " . htmlspecialchars(trim($response)) . "</div>";

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
                $response = fgets($socket, 515);
                echo "<div>Письмо отправлено: " . htmlspecialchars(trim($response)) . "</div>";

                if (substr($response, 0, 3) === "250") {
                    echo "<div style='color: green; font-weight: bold; font-size: 18px;'>🎉 SMTP работает!</div>";
                    echo "<p>Тестовое письмо отправлено на $to_email</p>";
                    
                    // Закрываем соединение
                    fwrite($socket, "QUIT\r\n");
                    fclose($socket);
                    
                    return true;
                } else {
                    echo "<div style='color: red;'>❌ Ошибка при отправке письма</div>";
                }
            } else {
                echo "<div style='color: red;'>❌ Ошибка аутентификации</div>";
                echo "<p>Возможные причины:</p>";
                echo "<ul>";
                echo "<li>Неверный пароль</li>";
                echo "<li>Нужно включить SMTP в настройках почты</li>";
                echo "<li>Требуется пароль для приложений</li>";
                echo "</ul>";
            }
        } else {
            echo "<div style='color: red;'>❌ Сервер не поддерживает аутентификацию</div>";
        }

        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return false;

    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</div>";
        return false;
    }
}

// Тестируем соединение
$smtp_works = testSMTPConnection();

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
if ($smtp_works) {
    echo "<a href='send_message_smtp_simple.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>📧 Использовать SMTP обработчик</a>";
}
echo "</div>";

// Инструкции по настройке Mail.ru
echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h3>📧 Как включить SMTP в Mail.ru:</h3>";
echo "<ol>";
echo "<li>Зайдите в вашу почту на <a href='https://mail.ru' target='_blank'>mail.ru</a></li>";
echo "<li>Перейдите в Настройки → Все настройки</li>";
echo "<li>Выберите раздел \"Почтовые программы\"</li>";
echo "<li>Включите опции:";
echo "<ul>";
echo "<li>☑️ \"С сервера imap.mail.ru через протокол IMAP\"</li>";
echo "<li>☑️ \"На сервер smtp.mail.ru через протокол SMTP\"</li>";
echo "</ul>";
echo "</li>";
echo "<li>Если основной пароль не работает, создайте пароль для приложений:</li>";
echo "<ul>";
echo "<li>Настройки → Безопасность → Пароли для внешних приложений</li>";
echo "<li>Создайте новый пароль и используйте его</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";
echo "</div>";
?>