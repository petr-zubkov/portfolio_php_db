<?php
// Принудительная установка сессии
session_start();
$_SESSION['admin'] = true;
$_SESSION['login_time'] = time();

echo '<!DOCTYPE html>
<html>
<head>
    <title>Экстренный доступ</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px; }
        button { background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <div class="success">
        <h1>✓ Доступ к админ-панели восстановлен!</h1>
        <p>Session ID: ' . session_id() . '</p>
        <p>Время: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    
    <button onclick="window.location.href=\'index.php\'">Перейти в админ-панель</button>
</body>
</html>';
?>