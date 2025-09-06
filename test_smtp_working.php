<?php
// Рабочий тест SMTP с аутентификацией
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🎯 Рабочий тест SMTP с аутентификацией</h1>";

require_once 'config.php';

echo "<div style='background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 5px;'>";
echo "<h3>Текущие настройки:</h3>";
echo "<strong>SMTP Host:</strong> " . SMTP_HOST . "<br>";
echo "<strong>SMTP Port:</strong> " . SMTP_PORT . "<br>";
echo "<strong>SMTP Username:</strong> " . SMTP_USERNAME . "<br>";
echo "<strong>From Email:</strong> " . SMTP_FROM_EMAIL . "<br>";
echo "<strong>To Email:</strong> " . SMTP_TO_EMAIL . "<br>";
echo "</div>";

function testSMTPWithAuth($host, $port, $username, $password, $method_name) {
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #28a745;'>";
    echo "<h3>🔧 Тест: $method_name</h3>";
    
    try {
        // Определяем тип подключения
        if ($port == 465) {
            $connection_string = "ssl://$host:$port";
            $secure = 'ssl';
        } elseif ($port == 587) {
            $connection_string = "$host:$port";
            $secure = 'tls';
        } else {
            $connection_string = "$host:$port";
            $secure = 'none';
        }
        
        echo "<div>Подключение: $connection_string (Secure: $secure)</div>";
        
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
            echo "<div style='color: red;'>❌ Ошибка подключения: $errstr ($errno)</div>";
            return false;
        }
        
        echo "<div style='color: green;'>✅ Подключено успешно</div>";
        
        // Устанавливаем таймаут
        stream_set_timeout($socket, 10);
        
        // Читаем приветствие
        $response = fgets($socket, 515);
        echo "<div>Приветствие: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        
        if (!$response || substr($response, 0, 3) !== "220") {
            echo "<div style='color: red;'>❌ Неверный ответ сервера</div>";
            fclose($socket);
            return false;
        }
        
        // Для TLS на порту 587 нужно сначала отправить STARTTLS
        if ($port == 587) {
            fwrite($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            echo "<div>STARTTLS: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
            
            if (substr($response, 0, 3) === "220") {
                // Включаем шифрование
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                echo "<div style='color: green;'>✅ TLS включен</div>";
            } else {
                echo "<div style='color: red;'>❌ STARTTLS не поддерживается</div>";
                fclose($socket);
                return false;
            }
            
            // Повторно отправляем EHLO после TLS
            fwrite($socket, "EHLO " . gethostname() . "\r\n");
        } else {
            // Для SSL сразу отправляем EHLO
            fwrite($socket, "EHLO " . gethostname() . "\r\n");
        }
        
        // Читаем ответ EHLO
        $ehlo_response = "";
        while (true) {
            $line = fgets($socket, 515);
            if (!$line) break;
            
            $ehlo_response .= $line;
            echo "<div>EHLO: <code>" . htmlspecialchars(trim($line)) . "</code></div>";
            
            if (substr($line, 3, 1) === " ") break;
        }
        
        // Проверяем поддержку AUTH
        if (strpos($ehlo_response, 'AUTH') !== false) {
            echo "<div style='color: green;'>✅ AUTH поддерживается</div>";
            
            // Начинаем аутентификацию LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            echo "<div>AUTH LOGIN: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
            
            if (substr($response, 0, 3) === "334") {
                // Отправляем логин в base64
                fwrite($socket, base64_encode($username) . "\r\n");
                $response = fgets($socket, 515);
                echo "<div>Username: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
                
                if (substr($response, 0, 3) === "334") {
                    // Отправляем пароль в base64
                    fwrite($socket, base64_encode($password) . "\r\n");
                    $response = fgets($socket, 515);
                    echo "<div>Password: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
                    
                    if (substr($response, 0, 3) === "235") {
                        echo "<div style='color: green; font-weight: bold;'>✅ Аутентификация успешна!</div>";
                        
                        // Отправляем тестовое письмо
                        $result = sendTestEmail($socket, SMTP_FROM_EMAIL, SMTP_TO_EMAIL);
                        
                        // Закрываем соединение
                        fwrite($socket, "QUIT\r\n");
                        fclose($socket);
                        
                        return $result;
                    } else {
                        echo "<div style='color: red; font-weight: bold;'>❌ Ошибка аутентификации!</div>";
                        echo "<div><strong>Код ошибки:</strong> " . substr($response, 0, 3) . "</div>";
                        
                        if (substr($response, 0, 3) === "535") {
                            echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                            echo "<strong>🔍 Возможные причины ошибки 535:</strong><br>";
                            echo "• Неверный пароль<br>";
                            echo "• Требуется пароль для приложений<br>";
                            echo "• SMTP не включен в настройках Mail.ru<br>";
                            echo "• IP адрес заблокирован<br>";
                            echo "</div>";
                        }
                    }
                } else {
                    echo "<div style='color: red;'>❌ Ошибка при отправке логина</div>";
                }
            } else {
                echo "<div style='color: red;'>❌ Сервер не готов к аутентификации</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ AUTH не поддерживается</div>";
        }
        
        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return false;
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</div>";
        return false;
    }
    
    echo "</div>";
}

function sendTestEmail($socket, $from, $to) {
    try {
        echo "<div style='background: #f0f8ff; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>📧 Отправка тестового письма...</strong></div>";
        
        // MAIL FROM
        fwrite($socket, "MAIL FROM:<$from>\r\n");
        $response = fgets($socket, 515);
        echo "<div>MAIL FROM: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        
        if (substr($response, 0, 3) !== "250") {
            echo "<div style='color: red;'>❌ Ошибка при установке отправителя</div>";
            return false;
        }
        
        // RCPT TO
        fwrite($socket, "RCPT TO:<$to>\r\n");
        $response = fgets($socket, 515);
        echo "<div>RCPT TO: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        
        if (substr($response, 0, 3) !== "250") {
            echo "<div style='color: red;'>❌ Ошибка при установке получателя</div>";
            return false;
        }
        
        // DATA
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        echo "<div>DATA: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        
        if (substr($response, 0, 3) !== "354") {
            echo "<div style='color: red;'>❌ Сервер не готов принять данные письма</div>";
            return false;
        }
        
        // Формируем письмо
        $subject = "Тест SMTP - Рабочий вариант";
        $body = "Это тестовое сообщение с сайта zubkov.space\n\n";
        $body .= "Время: " . date('Y-m-d H:i:s') . "\n";
        $body .= "Метод: " . ($_POST['method'] ?? 'SSL/TLS') . "\n";
        $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $body .= "Host: " . $_SERVER['HTTP_HOST'] . "\n\n";
        $body .= "Если вы видите это письмо, значит SMTP работает правильно!";
        
        $email_data = "From: $from\r\n";
        $email_data .= "To: $to\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "$body\r\n";
        $email_data .= ".\r\n";
        
        fwrite($socket, $email_data);
        $response = fgets($socket, 515);
        echo "<div>Отправка: <code>" . htmlspecialchars(trim($response)) . "</code></div>";
        
        if (substr($response, 0, 3) === "250") {
            echo "<div style='color: green; font-weight: bold; font-size: 18px; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>🎉 Письмо успешно отправлено!</div>";
            echo "<p>Тестовое письмо отправлено на <strong>$to</strong></p>";
            echo "<p>Проверьте ваш почтовый ящик!</p>";
            return true;
        } else {
            echo "<div style='color: red;'>❌ Ошибка при отправке письма</div>";
            return false;
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Ошибка отправки: " . $e->getMessage() . "</div>";
        return false;
    }
}

// Тестируем разные методы
$success = false;

echo "<h2>🧪 Тестирование разных методов подключения:</h2>";

// Метод 1: SSL на порту 465 (рекомендуемый)
$success = testSMTPWithAuth(
    SMTP_HOST, 
    465, 
    SMTP_USERNAME, 
    SMTP_PASSWORD, 
    "SSL на порту 465 (рекомендуется)"
) || $success;

// Метод 2: TLS на порту 587
$success = testSMTPWithAuth(
    SMTP_HOST, 
    587, 
    SMTP_USERNAME, 
    SMTP_PASSWORD, 
    "TLS на порту 587"
) || $success;

echo "<div style='margin: 30px 0; text-align: center;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";

if ($success) {
    echo "<a href='send_message_smtp_final.php' style='display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>📧 Использовать SMTP обработчик</a>";
    echo "<a href='index.php' style='display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 На сайт</a>";
}

echo "</div>";

// Рекомендации
if (!$success) {
    echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px; border: 1px solid #ffeaa7;'>";
    echo "<h3>⚠️ Рекомендации по исправлению:</h3>";
    echo "<ol>";
    echo "<li><strong>Проверьте пароль:</strong> Убедитесь, что в config.php указан правильный пароль</li>";
    echo "<li><strong>Пароль для приложений:</strong> Если основной пароль не работает, создайте пароль для приложений в Mail.ru</li>";
    echo "<li><strong>Включите SMTP:</strong> Настройки → Все настройки → Почтовые программы → Включите SMTP</li>";
    echo "<li><strong>Проверьте безопасность:</strong> Убедитесь, что нет двухфакторной аутентификации, блокирующей SMTP</li>";
    echo "</ol>";
    echo "</div>";
}
?>