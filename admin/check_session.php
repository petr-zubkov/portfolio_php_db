<?php
echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка сессии</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        form { margin: 20px 0; }
        input { margin: 5px; padding: 8px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Проверка сессии и доступ к админ-панели</h1>';

echo '<div class="info">';
echo '<h3>Текущее состояние сессии:</h3>';
echo 'Session ID: ' . session_id() . '<br>';
echo 'Session status: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . '<br>';
echo 'Session variables: <pre>' . print_r($_SESSION, true) . '</pre>';
echo '</div>';

if (!isset($_SESSION['admin'])) {
    echo '<div class="error">Вы не авторизованы в админ-панели</div>';
    
    echo '<form method="post">';
    echo '<h3>Войдите в админ-панель:</h3>';
    echo '<input type="text" name="username" placeholder="Логин" value="admin" required><br>';
    echo '<input type="password" name="password" placeholder="Пароль" value="password123" required><br>';
    echo '<button type="submit">Войти</button>';
    echo '</form>';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === 'admin' && $password === 'password123') {
            $_SESSION['admin'] = true;
            $_SESSION['login_time'] = time();
            echo '<div class="success">Вы успешно вошли в админ-панель!</div>';
            echo '<script>window.location.href = "index.php";</script>';
        } else {
            echo '<div class="error">Неверный логин или пароль</div>';
        }
    }
} else {
    echo '<div class="success">Вы авторизованы в админ-панели</div>';
    echo '<p>Время входа: ' . date('Y-m-d H:i:s', $_SESSION['login_time']) . '</p>';
    echo '<a href="index.php">Перейти в админ-панель</a><br>';
    echo '<a href="logout.php">Выйти</a>';
}

echo '<div class="info">';
echo '<h3>Информация о файлах:</h3>';
echo 'index.php существует: ' . (file_exists(__DIR__ . '/index.php') ? 'Да' : 'Нет') . '<br>';
echo 'final_index.php существует: ' . (file_exists(__DIR__ . '/final_index.php') ? 'Да' : 'Нет') . '<br>';
echo 'Права на папку admin: ' . substr(sprintf('%o', fileperms(__DIR__)), -4) . '<br>';
echo '</div>';

echo '<div class="info">';
echo '<h3>Действия:</h3>';
echo '<a href="restore_access.php">Восстановить доступ к админ-панели</a><br>';
echo '<a href="create_admin_files.php">Создать недостающие файлы</a><br>';
echo '<a href="test_auth.php">Тестировать авторизацию</a>';
echo '</div>';

echo '</body></html>';
?>