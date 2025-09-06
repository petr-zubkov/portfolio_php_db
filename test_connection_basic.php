<?php
// Базовый тест соединения с SMTP сервером
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔌 Базовый тест соединения</h1>";

require_once 'config.php';

function testBasicConnection($host, $port, $use_ssl = true) {
    echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h3>Тест: $host:$port" . ($use_ssl ? " (SSL)" : "") . "</h3>";
    
    try {
        $connection_string = $use_ssl ? "ssl://$host:$port" : "$host:$port";
        echo "<div>Попытка подключения к: $connection_string</div>";
        
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            ]
        ]);
        
        $socket = @stream_socket_client(
            $connection_string,
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            echo "<div style='color: red;'>❌ Ошибка: $errstr ($errno)</div>";
            return false;
        }
        
        echo "<div style='color: green;'>✅ Соединение установлено</div>";
        
        // Читаем приветствие
        stream_set_timeout($socket, 5);
        $greeting = fgets($socket, 515);
        
        if ($greeting) {
            echo "<div>Приветствие сервера: <code>" . htmlspecialchars(trim($greeting)) . "</code></div>";
            
            if (substr($greeting, 0, 3) === "220") {
                echo "<div style='color: green;'>✅ Сервер готов (220)</div>";
                
                // Пробуем отправить EHLO
                fwrite($socket, "EHLO test\r\n");
                $ehlo_response = fgets($socket, 515);
                echo "<div>EHLO ответ: <code>" . htmlspecialchars(trim($ehlo_response)) . "</code></div>";
                
                // Читаем все строки ответа EHLO
                while (true) {
                    $line = fgets($socket, 515);
                    if (!$line) break;
                    echo "<div>EHLO строка: <code>" . htmlspecialchars(trim($line)) . "</code></div>";
                    if (substr($line, 3, 1) === " ") break;
                }
                
                fclose($socket);
                return true;
            } else {
                echo "<div style='color: red;'>❌ Сервер не готов: " . substr($greeting, 0, 3) . "</div>";
            }
        } else {
            echo "<div style='color: orange;'>⚠️ Нет приветствия от сервера</div>";
        }
        
        fclose($socket);
        return false;
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Исключение: " . $e->getMessage() . "</div>";
        return false;
    }
    
    echo "</div>";
}

// Тестируем разные варианты подключения
echo "<h2>🧪 Тестирование различных вариантов подключения:</h2>";

// Вариант 1: SSL на 465
testBasicConnection(SMTP_HOST, 465, true);

// Вариант 2: TLS на 587
testBasicConnection(SMTP_HOST, 587, false);

// Вариант 3: Без шифрования на 25
testBasicConnection(SMTP_HOST, 25, false);

// Вариант 4: Попробуем альтернативные хосты
echo "<h2>🌐 Тестирование альтернативных хостов:</h2>";
testBasicConnection("smtp.mail.ru", 465, true);
testBasicConnection("mail.ru", 25, false);

echo "<div style='margin: 20px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Назад</a>";
echo "</div>";

// Информация о сервере
echo "<div style='background: #e9ecef; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>ℹ️ Информация о сервере:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? '✅ Включен' : '❌ Отключен') . "</p>";
echo "<p><strong>Stream Sockets:</strong> " . (function_exists('stream_socket_client') ? '✅ Доступны' : '❌ Недоступны') . "</p>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "</div>";
?>