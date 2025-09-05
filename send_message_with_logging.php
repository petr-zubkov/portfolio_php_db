<?php
// Обработчик с подробным логированием
header('Content-Type: application/json; charset=utf-8');

// Включаем логирование
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Буферизация вывода
ob_start();

try {
    // Логируем начало
    error_log("=== Начало обработки сообщения ===");
    error_log("Время: " . date('Y-m-d H:i:s'));
    error_log("Метод: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST данные: " . print_r($_POST, true));
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен. Используйте POST.');
    }

    // Получаем данные
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    error_log("Имя: $name");
    error_log("Email: $email");
    error_log("Сообщение: " . substr($message, 0, 100));

    // Валидация
    if (empty($name)) throw new Exception('Введите имя');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Некорректный email');
    if (empty($message)) throw new Exception('Введите сообщение');

    // Защита
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    require_once 'config.php';
    error_log("Конфигурация подключена");

    // Сохраняем в БД
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'new')");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        error_log("Сообщение сохранено в БД, ID: " . $conn->insert_id);
    } else {
        error_log("Ошибка сохранения в БД: " . $conn->error);
    }

    // Пробуем разные методы отправки
    $mail_sent = false;
    $method_used = '';

    // Метод 1: Стандартная функция mail()
    error_log("Пробуем метод 1: mail()");
    $to = defined('SMTP_TO_EMAIL') ? SMTP_TO_EMAIL : 'petr-zubkov@mail.ru';
    $subject = 'Новое сообщение с сайта zubkov.space';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: zubkov.space <noreply@zubkov.space>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $headers .= "Return-Path: noreply@zubkov.space\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Originating-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

    $body = "Новое сообщение с сайта zubkov.space\n\n";
    $body .= "========================================\n";
    $body .= "Имя: $name\n";
    $body .= "Email: $email\n";
    $body .= "Дата: " . date('d.m.Y H:i') . "\n";
    $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $body .= "========================================\n\n";
    $body .= "Сообщение:\n$message\n\n";
    $body .= "========================================\n";
    $body .= "Отправлено с сайта: https://zubkov.space\n";

    if (mail($to, $subject, $body, $headers)) {
        $mail_sent = true;
        $method_used = 'mail()';
        error_log("✅ Письмо отправлено через mail()");
    } else {
        error_log("❌ mail() не сработала");
        
        // Метод 2: Альтернативные заголовки
        error_log("Пробуем метод 2: альтернативные заголовки");
        $alt_headers = "From: webmaster@zubkov.space\r\n";
        $alt_headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        if (mail($to, $subject, $body, $alt_headers)) {
            $mail_sent = true;
            $method_used = 'mail() с альтернативными заголовками';
            error_log("✅ Письмо отправлено через mail() с альтернативными заголовками");
        } else {
            error_log("❌ Альтернативные заголовки тоже не сработали");
        }
    }

    // Формируем ответ
    ob_end_clean();
    
    if ($mail_sent) {
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method_used' => $method_used,
                'saved_to_db' => true,
                'to_email' => $to,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
        error_log("✅ Успешная отправка ответа");
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Сообщение сохранено, но возникла проблема с отправкой письма. Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'method_used' => 'ни один метод не сработал',
                'saved_to_db' => true,
                'to_email' => $to,
                'error' => 'Все методы отправки почты вернули false'
            ]
        ]);
        error_log("❌ Не удалось отправить письмо, но данные сохранены");
    }

} catch (Exception $e) {
    error_log("❌ Ошибка: " . $e->getMessage());
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST
        ]
    ]);
}

error_log("=== Завершение обработки сообщения ===");
exit;
?>