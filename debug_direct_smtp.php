<?php
// Отладка send_message_direct_smtp.php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Отладка send_message_direct_smtp.php</h1>";

// Имитируем POST запрос с данными формы
$_POST['name'] = 'Тест с формы';
$_POST['email'] = 'test@example.com';
$_POST['message'] = 'Тестовое сообщение с формы для проверки SMTP';

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📤 Тестовые данные:</h3>";
echo "<div><strong>Имя:</strong> " . htmlspecialchars($_POST['name']) . "</div>";
echo "<div><strong>Email:</strong> " . htmlspecialchars($_POST['email']) . "</div>";
echo "<div><strong>Сообщение:</strong> " . htmlspecialchars($_POST['message']) . "</div>";
echo "</div>";

// Подключаем конфигурацию
require_once 'config.php';

echo "<div style='background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>⚙️ Настройки SMTP:</h3>";
echo "<div><strong>Host:</strong> " . SMTP_HOST . "</div>";
echo "<div><strong>Port:</strong> " . SMTP_PORT . "</div>";
echo "<div><strong>Username:</strong> " . SMTP_USERNAME . "</div>";
echo "<div><strong>Password:</strong> " . (SMTP_PASSWORD === 'your_password_here' ? '❌ Не настроен' : '✅ Настроен') . "</div>";
echo "<div><strong>From:</strong> " . SMTP_FROM_EMAIL . "</div>";
echo "<div><strong>To:</strong> " . SMTP_TO_EMAIL . "</div>";
echo "</div>";

// Проверяем функцию sendDirectSMTP
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>🧪 Тест функции sendDirectSMTP:</h3>";

try {
    $result = sendDirectSMTP($_POST['name'], $_POST['email'], $_POST['message']);
    
    if ($result) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h4>✅ Успех!</h4>";
        echo "<p>Функция sendDirectSMTP вернула TRUE</p>";
        echo "<p>Письмо должно быть отправлено на " . SMTP_TO_EMAIL . "</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<h4>❌ Ошибка!</h4>";
        echo "<p>Функция sendDirectSMTP вернула FALSE</p>";
        echo "<p>Письмо не было отправлено</p>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>❌ Исключение!</h4>";
    echo "<p>Ошибка: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div>";

// Проверяем базу данных
echo "<div style='background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>💾 Проверка базы данных:</h3>";

try {
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'test')");
    $stmt->bind_param("sss", $_POST['name'], $_POST['email'], $_POST['message']);
    $db_result = $stmt->execute();
    
    if ($db_result) {
        echo "<div style='color: green;'>✅ Сообщение сохранено в базу данных</div>";
    } else {
        echo "<div style='color: red;'>❌ Ошибка сохранения в базу данных</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Ошибка базы данных: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Выводим код функции для проверки
echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3>📋 Код функции sendDirectSMTP:</h3>";
echo "<pre style='background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;'>";
echo htmlspecialchars(file_get_contents('send_message_direct_smtp.php'));
echo "</pre>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='javascript:history.back()' style='display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>← Назад</a>";
echo "<a href='test_smtp_working.php' style='display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Тест SMTP</a>";
echo "</div>";

// Копируем функцию для тестирования
function sendDirectSMTP($name, $email, $message) {
    try {
        require_once 'config.php';
        
        $host = SMTP_HOST;
        $port = SMTP_PORT;
        $username = SMTP_USERNAME;
        $password = SMTP_PASSWORD;
        $from_email = SMTP_FROM_EMAIL;
        $to_email = SMTP_TO_EMAIL;
        
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Отладка:</strong> Подключение к $host:$port<br>";
        echo "</div>";
        
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
            "ssl://$host:$port",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
            echo "<strong>Ошибка подключения:</strong> $errstr ($errno)<br>";
            echo "</div>";
            return false;
        }
        
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Подключено успешно</strong><br>";
        echo "</div>";
        
        // Читаем приветствие сервера
        $greeting = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Приветствие:</strong> " . htmlspecialchars(trim($greeting)) . "<br>";
        echo "</div>";
        
        // EHLO
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $ehlo_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>EHLO:</strong> " . htmlspecialchars(trim($ehlo_response)) . "<br>";
        echo "</div>";
        
        // Аутентификация
        fwrite($socket, "AUTH LOGIN\r\n");
        $auth_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>AUTH:</strong> " . htmlspecialchars(trim($auth_response)) . "<br>";
        echo "</div>";
        
        fwrite($socket, base64_encode($username) . "\r\n");
        $user_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>User:</strong> " . htmlspecialchars(trim($user_response)) . "<br>";
        echo "</div>";
        
        fwrite($socket, base64_encode($password) . "\r\n");
        $pass_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Password:</strong> " . htmlspecialchars(trim($pass_response)) . "<br>";
        echo "</div>";
        
        if (substr($pass_response, 0, 3) !== "235") {
            echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
            echo "<strong>Ошибка аутентификации:</strong> " . substr($pass_response, 0, 3) . "<br>";
            echo "</div>";
            fclose($socket);
            return false;
        }
        
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Аутентификация успешна!</strong><br>";
        echo "</div>";
        
        // Отправитель
        fwrite($socket, "MAIL FROM:<$from_email>\r\n");
        $mail_from = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>MAIL FROM:</strong> " . htmlspecialchars(trim($mail_from)) . "<br>";
        echo "</div>";
        
        // Получатель
        fwrite($socket, "RCPT TO:<$to_email>\r\n");
        $rcpt_to = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>RCPT TO:</strong> " . htmlspecialchars(trim($rcpt_to)) . "<br>";
        echo "</div>";
        
        // Данные письма
        fwrite($socket, "DATA\r\n");
        $data_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>DATA:</strong> " . htmlspecialchars(trim($data_response)) . "<br>";
        echo "</div>";
        
        // Формируем письмо
        $subject = 'Новое сообщение с сайта zubkov.space';
        $email_data = "From: $from_email\r\n";
        $email_data .= "To: $to_email\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "Reply-To: $email\r\n";
        $email_data .= "MIME-Version: 1.0\r\n";
        $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_data .= "\r\n";
        $email_data .= "Имя: $name\r\n";
        $email_data .= "Email: $email\r\n";
        $email_data .= "Дата: " . date('d.m.Y H:i') . "\r\n";
        $email_data .= "\r\n";
        $email_data .= "Сообщение:\r\n";
        $email_data .= "$message\r\n";
        $email_data .= "\r\n.\r\n";
        
        fwrite($socket, $email_data);
        $final_response = fgets($socket, 515);
        echo "<div style='background: #e2e3e5; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Final Response:</strong> " . htmlspecialchars(trim($final_response)) . "<br>";
        echo "</div>";
        
        // Закрываем соединение
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        $success = substr($final_response, 0, 3) === "250";
        
        if ($success) {
            echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
            echo "<strong>✅ Письмо отправлено успешно!</strong><br>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
            echo "<strong>❌ Ошибка отправки письма:</strong> " . substr($final_response, 0, 3) . "<br>";
            echo "</div>";
        }
        
        return $success;
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Exception:</strong> " . $e->getMessage() . "<br>";
        echo "</div>";
        return false;
    }
}
?>