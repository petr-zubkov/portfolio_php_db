<?php
// Резервная копия рабочего обработчика
// Копия send_message_simple.php

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

// Устанавливаем заголовки
header('Content-Type: application/json; charset=utf-8');

try {
    // Проверяем, что это POST запрос
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен. Используйте POST.');
    }

    // Получаем данные из формы
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Валидация данных
    if (empty($name)) {
        throw new Exception('Пожалуйста, введите ваше имя');
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Пожалуйста, введите корректный email');
    }

    if (empty($message)) {
        throw new Exception('Пожалуйста, введите ваше сообщение');
    }

    // Защита от XSS
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Подключаем конфигурацию
    require_once 'config.php';

    // Сохраняем сообщение в базу данных
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'new')");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if (!$stmt->execute()) {
        throw new Exception('Ошибка при сохранении сообщения в базу данных: ' . $conn->error);
    }

    // Отправляем письмо через функцию mail()
    $to = defined('SMTP_TO_EMAIL') ? SMTP_TO_EMAIL : 'petr-zubkov@mail.ru';
    $subject = 'Новое сообщение с сайта zubkov.space';
    
    // Формируем заголовки
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: " . (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@zubkov.space') . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-MSMail-Priority: Normal\r\n";
    
    // Формируем тело письма
    $body = "Новое сообщение с сайта zubkov.space\n\n";
    $body .= "========================================\n";
    $body .= "Имя: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Дата: " . date('d.m.Y H:i') . "\n";
    $body .= "IP адрес: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $body .= "========================================\n\n";
    $body .= "Сообщение:\n" . $message . "\n\n";
    $body .= "========================================\n";
    $body .= "Это сообщение было отправлено через контактную форму сайта zubkov.space\n";

    // Отправляем письмо
    $mail_sent = mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        // Дополнительно отправляем уведомление на второй email (если настроен)
        $admin_email = defined('SMTP_USERNAME') ? SMTP_USERNAME : null;
        if ($admin_email && $admin_email !== $to) {
            mail($admin_email, $subject, $body, $headers);
        }
        
        // Очищаем буфер и отправляем успешный ответ
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение успешно отправлено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'saved_to_db' => true,
                'email_sent' => true,
                'to_email' => $to
            ]
        ]);
    } else {
        throw new Exception('Ошибка при отправке письма. Функция mail() вернула false.');
    }

} catch (Exception $e) {
    // Очищаем буфер и отправляем ошибку
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при отправке сообщения: ' . $e->getMessage(),
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'error' => $e->getMessage()
        ]
    ]);
}

exit;
?>