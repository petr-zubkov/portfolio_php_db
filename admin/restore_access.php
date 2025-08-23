<?php
echo '<!DOCTYPE html>
<html>
<head>
    <title>Восстановление доступа к админ-панели</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 4px; }
        form { margin: 20px 0; }
        input, textarea { width: 100%; margin: 5px 0; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
    </style>
</head>
<body>
    <h1>Восстановление доступа к админ-панели</h1>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Запускаем сессию
    session_start();
    
    // Устанавливаем переменную сессии
    $_SESSION['admin'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    
    echo '<div class="success">✓ Доступ к админ-панели восстановлен!</div>';
    echo '<div class="info">';
    echo 'Session ID: ' . session_id() . '<br>';
    echo 'Время входа: ' . date('Y-m-d H:i:s') . '<br>';
    echo 'IP адрес: ' . $_SESSION['ip'] . '<br>';
    echo 'User Agent: ' . $_SESSION['user_agent'] . '<br>';
    echo '</div>';
    
    echo '<script>
        setTimeout(function() {
            window.location.href = "index.php";
        }, 2000);
    </script>';
    
} else {
    echo '<div class="info">';
    echo '<p>Эта страница восстановит ваш доступ к админ-панели.</p>';
    echo '<p>Нажмите кнопку ниже, чтобы восстановить доступ.</p>';
    echo '</div>';
    
    echo '<form method="post">';
    echo '<button type="submit">Восстановить доступ к админ-панели</button>';
    echo '</form>';
    
    echo '<div class="info">';
    echo '<h3>Текущее состояние:</h3>';
    echo 'Сессия запущена: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Да' : 'Нет') . '<br>';
    echo 'Переменная admin в сессии: ' . (isset($_SESSION['admin']) ? 'Установлена' : 'Не установлена') . '<br>';
    echo '</div>';
}

echo '<br><a href="check_session.php">Вернуться к проверке сессии</a>';
echo '</body></html>';
?>