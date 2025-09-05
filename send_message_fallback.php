<?php
// Резервный обработчик - работает даже если все остальное не работает
header('Content-Type: application/json; charset=utf-8');

// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Метод не разрешен. Используйте POST.');
    }

    // Получаем данные
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Валидация
    if (empty($name)) throw new Exception('Введите имя');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Некорректный email');
    if (empty($message)) throw new Exception('Введите сообщение');

    // Защита
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Подключаем конфигурацию
    require_once 'config.php';

    // Сохраняем в базу данных (самое важное!)
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message, status) VALUES (?, ?, ?, 'new')");
    $stmt->bind_param("sss", $name, $email, $message);
    $db_saved = $stmt->execute();

    $results = [];

    // Метод 1: Самая простая отправка через mail()
    $subject = 'Новое сообщение с сайта zubkov.space';
    $simple_headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $simple_body = "Имя: $name\nEmail: $email\n\n$message";
    
    $mail_sent = mail(SMTP_TO_EMAIL, $subject, $simple_body, $simple_headers);
    $results['simple_mail'] = $mail_sent;

    // Метод 2: Отправка на тот же email (иногда помогает)
    $mail_sent2 = mail($email, 'Копия вашего сообщения', "Ваше сообщение:\n\n$message", "From: " . SMTP_FROM_EMAIL);
    $results['copy_to_user'] = $mail_sent2;

    // Метод 3: Запись в файл (гарантированно работает)
    $log_file = __DIR__ . '/message_backup_' . date('Y-m-d') . '.txt';
    $log_entry = date('Y-m-d H:i:s') . "\n";
    $log_entry .= "Name: $name\n";
    $log_entry .= "Email: $email\n";
    $log_entry .= "Message: $message\n";
    $log_entry .= "----------------------------------------\n";
    
    $file_saved = file_put_contents($log_file, $log_entry, FILE_APPEND) !== false;
    $results['file_backup'] = $file_saved;

    // Проверяем, что хотя бы что-то сработало
    $any_success = $db_saved || $mail_sent || $mail_sent2 || $file_saved;

    if ($any_success) {
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Ваше сообщение получено! Я свяжусь с вами в ближайшее время.',
            'debug' => [
                'db_saved' => $db_saved,
                'email_sent' => $mail_sent,
                'copy_sent' => $mail_sent2,
                'file_saved' => $file_saved,
                'note' => 'Сообщение сохранено в базе данных и будет обработано вручную при необходимости.'
            ]
        ]);
    } else {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Произошла критическая ошибка. Пожалуйста, свяжитесь со мной напрямую.',
            'debug' => [
                'all_methods_failed' => true,
                'db_saved' => $db_saved
            ]
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage()
        ]
    ]);
}

exit;
?>