<?php
echo '<!DOCTYPE html>
<html>
<head>
    <title>Тест авторизации</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Тест авторизации</h1>';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo '<div class="info">';
    echo '<h3>Попытка входа:</h3>';
    echo 'Логин: ' . htmlspecialchars($username) . '<br>';
    echo 'Пароль: ' . htmlspecialchars($password) . '<br>';
    echo '</div>';
    
    if ($username === 'admin' && $password === 'password123') {
        $_SESSION['admin'] = true;
        $_SESSION['login_time'] = time();
        
        echo '<div class="success">✓ Вход выполнен успешно!</div>';
        echo '<div class="info">';
        echo 'Session ID: ' . session_id() . '<br>';
        echo 'Время входа: ' . date('Y-m-d H:i:s') . '<br>';
        echo '</div>';
        
        echo '<script>
            setTimeout(function() {
                window.location.href = "index.php";
            }, 2000);
        </script>';
    } else {
        echo '<div class="error">✗ Неверный логин или пароль</div>';
    }
} else {
    echo '<div class="info">';
    echo '<p>Тестирование системы авторизации.</p>';
    echo '<p>Используйте стандартные данные:</p>';
    echo '<ul>';
    echo '<li>Логин: admin</li>';
    echo '<li>Пароль: password123</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<form method="post">';
    echo '<div class="form-group">';
    echo '<label>Логин:</label>';
    echo '<input type="text" name="username" value="admin" required>';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label>Пароль:</label>';
    echo '<input type="password" name="password" value="password123" required>';
    echo '</div>';
    echo '<button type="submit">Войти</button>';
    echo '</form>';
}

echo '<div class="info">';
echo '<h3>Текущее состояние сессии:</h3>';
echo 'Session ID: ' . session_id() . '<br>';
echo 'Session активна: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Да' : 'Нет') . '<br>';
echo 'Переменная admin: ' . (isset($_SESSION['admin']) ? 'Установлена' : 'Не установлена') . '<br>';
if (isset($_SESSION['login_time'])) {
    echo 'Время входа: ' . date('Y-m-d H:i:s', $_SESSION['login_time']) . '<br>';
}
echo '</div>';

echo '<br><a href="check_session.php">Вернуться к проверке сессии</a>';
echo '<br><a href="restore_access.php">Восстановить доступ</a>';
echo '</body></html>';
?>