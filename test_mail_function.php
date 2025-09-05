<?php
// Детальный тест функции mail()
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Тест функции mail()</h1>";

// Включаем вывод ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем, включена ли функция mail()
if (!function_exists('mail')) {
    echo "<div style='color: red; font-weight: bold;'>❌ Функция mail() отключена на сервере</div>";
    echo "<p>Нужно использовать альтернативные методы отправки.</p>";
    exit;
}

echo "<div style='color: green;'>✅ Функция mail() доступна</div>";

// Тестируем отправку письма
$to = 'petr-zubkov@mail.ru';
$subject = 'Тестовое сообщение с сайта zubkov.space';
$message = "Это тестовое сообщение для проверки работы функции mail().\n\n";
$message .= "Время отправки: " . date('Y-m-d H:i:s') . "\n";
$message .= "Сервер: " . $_SERVER['HTTP_HOST'] . "\n";
$message .= "IP: " . $_SERVER['SERVER_ADDR'] . "\n";

$headers = "From: test@zubkov.space\r\n";
$headers .= "Reply-To: test@zubkov.space\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

echo "<h2>Попытка отправить письмо...</h2>";
echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>";
echo "<strong>Кому:</strong> $to<br>";
echo "<strong>Тема:</strong> $subject<br>";
echo "<strong>Заголовки:</strong><pre>" . htmlspecialchars($headers) . "</pre>";
echo "<strong>Сообщение:</strong><pre>" . htmlspecialchars($message) . "</pre>";
echo "</div>";

// Отправляем письмо
$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    echo "<div style='color: green; font-weight: bold;'>✅ Функция mail() вернула TRUE</div>";
    echo "<p>Письмо должно было быть отправлено. Проверьте почту через 1-5 минут.</p>";
    
    // Дополнительная информация
    echo "<h3>Дополнительная информация:</h3>";
    echo "<div style='background: #e8f4f8; padding: 10px; margin: 10px 0;'>";
    echo "<strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "<br>";
    echo "<strong>SMTP:</strong> " . ini_get('SMTP') . "<br>";
    echo "<strong>smtp_port:</strong> " . ini_get('smtp_port') . "<br>";
    echo "<strong>sendmail_from:</strong> " . ini_get('sendmail_from') . "<br>";
    echo "</div>";
    
} else {
    echo "<div style='color: red; font-weight: bold;'>❌ Функция mail() вернула FALSE</div>";
    echo "<p>Ошибка при отправке письма. Возможные причины:</p>";
    echo "<ul>";
    echo "<li>На сервере не настроен sendmail</li>";
    echo "<li>Хостинг блокирует отправку писем</li>";
    echo "<li>Неверные настройки SMTP</li>";
    echo "<li>Письмо попало в спам-фильтр</li>";
    echo "</ul>";
    
    // Показываем ошибки
    $error = error_get_last();
    if ($error) {
        echo "<h3>Последняя ошибка:</h3>";
        echo "<div style='color: red; background: #ffe6e6; padding: 10px;'>";
        echo "<strong>" . $error['message'] . "</strong><br>";
        echo "Файл: " . $error['file'] . "<br>";
        echo "Строка: " . $error['line'];
        echo "</div>";
    }
}

// Тестируем разные варианты заголовков
echo "<h2>Тест с альтернативными заголовками:</h2>";

$alt_headers = "MIME-Version: 1.0\r\n";
$alt_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$alt_headers .= "From: noreply@zubkov.space\r\n";
$alt_headers .= "Reply-To: noreply@zubkov.space\r\n";
$alt_headers .= "Return-Path: noreply@zubkov.space\r\n";
$alt_headers .= "X-Sender: noreply@zubkov.space\r\n";
$alt_headers .= "X-Priority: 3\r\n";
$alt_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$alt_sent = mail($to, 'Тест #2 - Альтернативные заголовки', $message, $alt_headers);

if ($alt_sent) {
    echo "<div style='color: green;'>✅ Альтернативные заголовки - УСПЕХ</div>";
} else {
    echo "<div style='color: red;'>❌ Альтернативные заголовки - ОШИБКА</div>";
}

// Рекомендации
echo "<h2>Рекомендации:</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h3>Если письма не приходят:</h3>";
echo "<ol>";
echo "<li>Проверьте папку 'Спам' в почте</li>";
echo "<li>Проверьте настройки DKIM и SPF для домена zubkov.space</li>";
echo "<li>Свяжитесь с поддержкой хостинга о настройке почты</li>";
echo "<li>Используйте SMTP вместо функции mail()</li>";
echo "<li>Попробуйте внешние сервисы отправки почты</li>";
echo "</ol>";
echo "</div>";

// Кнопка для теста SMTP
echo "<h2>Альтернативные методы:</h2>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='test_smtp.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Тест SMTP</a>";
echo " ";
echo "<a href='send_message_external.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Внешний сервис</a>";
echo "</div>";
?>