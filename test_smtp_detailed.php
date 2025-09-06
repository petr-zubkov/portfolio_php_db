<?php
// Детальный тест SMTP с расширенной диагностикой
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Детальный тест SMTP</h1>";

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

// Функция детального тестирования SMTP
function testSMTPDetailed() {
    try {
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;

        echo "<h3>🔌 Тестирование соединения с $host:$port...</h3>";

        // Создаем контекст для SSL с более детальными настройками
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
                "crypto_method" => STREAM_CRYPTO_METHOD_TLS_CLIENT
            ]
        ]);

        // Устанавливаем таймаут
        $timeout = 30;
        
        echo "<div>Попытка подключения...</div>";
        
        // Подключаемся к SMTP серверу
        $socket = @stream_socket_client(
            "ssl://$host:$port",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            echo "<div style='color: red;'>❌ Не удалось подключиться к SMTP серверу</div>";
            echo "<div><strong>Код ошибки:</strong> $errno</div>";
            echo "<div><strong>Сообщение:</strong> $errstr</div>";
            return false;
        }

        echo "<div style='color: green;'>✅ Соединение установлено</div>";

        // Устанавливаем таймаут для чтения
        stream_set_timeout($socket, 10);

        // Читаем приветствие сервера
        echo "<div><strong>Ожидание приветствия сервера...</strong></div>";
        $response = fgets($socket, 515);
        if ($response) {
            echo "<div>Сервер: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        } else {
            echo "<div style='color: orange;'>⚠️ Сервер не отправил приветствие</div>";
        }

        // Проверяем код ответа
        if ($response && substr($response, 0, 3) === "220") {
            echo "<div style='color: green;'>✅ Сервер готов к работе</div>";
        } else {
            echo "<div style='color: red;'>❌ Сервер не готов</div>";
        }

        // Отправляем EHLO
        echo "<div><strong>Отправка EHLO...</strong></div>";
        $hostname = gethostname();
        fwrite($socket, "EHLO $hostname\r\n");
        
        // Читаем все строки ответа EHLO
        $ehlo_response = "";
        while (true) {
            $line = fgets($socket, 515);
            if (!$line) break;
            
            $ehlo_response .= $line;
            echo "<div>EHLO ответ: <code>" . htmlspecialchars(trim($line)) . "</code></div>";
            
            // Проверяем конец ответа (последняя строка начинается с пробела или содержит 250)
            if (substr($line, 3, 1) === " " || substr($line, 0, 3) === "250") {
                break;
            }
        }

        // Проверяем поддержку аутентификации
        if (strpos($ehlo_response, 'AUTH') !== false || strpos($ehlo_response, '250-AUTH') !== false) {
            echo "<div style='color: green;'>✅ Аутентификация поддерживается</div>";
            
            // Определяем доступные методы аутентификации
            if (strpos($ehlo_response, 'LOGIN') !== false) {
                echo "<div>Доступен метод: LOGIN</div>";
                $auth_method = 'LOGIN';
            } elseif (strpos($ehlo_response, 'PLAIN') !== false) {
                echo "<div>Доступен метод: PLAIN</div>";
                $auth_method = 'PLAIN';
            } else {
                echo "<div style='color: orange;'>⚠️ Неизвестный метод аутентификации</div>";
            }

            // Пробуем аутентификацию
            echo "<div><strong>Начало аутентификации...</strong></div>";
            
            if ($auth_method === 'LOGIN') {
                // Отправляем AUTH LOGIN
                fwrite($socket, "AUTH LOGIN\r\n");
                $response = fgets($socket, 515);
                echo "<div>AUTH LOGIN: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

                if (substr($response, 0, 3) === "334") {
                    // Отправляем логин
                    fwrite($socket, base64_encode($username) . "\r\n");
                    $response = fgets($socket, 515);
                    echo "<div>Username: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

                    if (substr($response, 0, 3) === "334") {
                        // Отправляем пароль
                        fwrite($socket, base64_encode($password) . "\r\n");
                        $response = fgets($socket, 515);
                        echo "<div>Password: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

                        if (substr($response, 0, 3) === "235") {
                            echo "<div style='color: green;'>✅ Аутентификация успешна!</div>";
                            
                            // Продолжаем с отправкой письма
                            return testEmailSending($socket, $from_email, $to_email);
                            
                        } else {
                            echo "<div style='color: red;'>❌ Ошибка аутентификации</div>";
                            echo "<div><strong>Возможные причины:</strong></div>";
                            echo "<ul>";
                            echo "<li>Неверный пароль</li>";
                            echo "<li>Требуется пароль для приложений</li>";
                            echo "<li>SMTP не включен в настройках Mail.ru</li>";
                            echo "</ul>";
                        }
                    } else {
                        echo "<div style='color: red;'>❌ Ошибка при отправке логина</div>";
                    }
                } else {
                    echo "<div style='color: red;'>❌ Сервер не готов к аутентификации</div>";
                }
            }
        } else {
            echo "<div style='color: red;'>❌ Сервер не поддерживает аутентификацию</div>";
            echo "<div>Полный ответ EHLO:</div>";
            echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($ehlo_response) . "</pre>";
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

function testEmailSending($socket, $from_email, $to_email) {
    try {
        echo "<div><strong>📧 Тестовая отправка письма...</strong></div>";
        
        // Отправитель
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        $response = fgets($socket, 515);
        echo "<div>MAIL FROM: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

        if (substr($response, 0, 3) !== "250") {
            echo "<div style='color: red;'>❌ Ошибка при установке отправителя</div>";
            return false;
        }

        // Получатель
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        $response = fgets($socket, 515);
        echo "<div>RCPT TO: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

        if (substr($response, 0, 3) !== "250") {
            echo "<div style='color: red;'>❌ Ошибка при установке получателя</div>";
            return false;
        }

        // Данные письма
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        echo "<div>DATA: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

        if (substr($response, 0, 3) !== "354") {
            echo "<div style='color: red;'>❌ Сервер не готов принять данные письма</div>";
            return false;
        }

        // Формируем тестовое письмо
        $email_data = "From: $from_email\r\n";
        $email_data .= "To: $to_email\r\n";
        $email_data .= "Subject: Тест SMTP zubkov.space - Детальный тест\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "Это тестовое сообщение для проверки работы SMTP.\r\n";
        $email_data .= "Время: " . date('Y-m-d H:i:s') . "\r\n";
        $email_data .= "Сервер: " . $_SERVER['HTTP_HOST'] . "\r\n";
        $email_data .= "Тест: Детальная диагностика SMTP\r\n";
        $email_data .= "\r\n.\r\n";

        fwrite($socket, $email_data);
        $response = fgets($socket, 515);
        echo "<div>Отправка письма: <code>" . htmlspecialchars(trim($response)) . "</code></div>";

        if (substr($response, 0, 3) === "250") {
            echo "<div style='color: green; font-weight: bold; font-size: 18px;'>🎉 Письмо успешно отправлено!</div>";
            echo "<p>Тестовое письмо отправлено на $to_email</p>";
            return true;
        } else {
            echo "<div style='color: red;'>❌ Ошибка при отправке письма</div>";
            return false;
        }

    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Ошибка при отправке письма: " . $e->getMessage() . "</div>";
        return false;
    }
}

// Тестируем соединение
$smtp_works = testSMTPDetailed();

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
if ($smtp_works) {
    echo "<a href='send_message_smtp_final.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>📧 Использовать SMTP обработчик</a>";
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
echo "<li>Создайте новый пароль и используйте его в config.php</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";
echo "</div>";

// Дополнительная информация
echo "<div style='background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
echo "<h4>🔍 Дополнительная диагностика:</h4>";
echo "<p><strong>Текущий сервер:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Время выполнения:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Версия PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Расширения:</strong> " . implode(', ', get_loaded_extensions()) . "</p>";
echo "</div>";
?>