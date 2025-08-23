<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка заголовков</title>
</head>
<body>
    <h1>Проверка ответа save_settings.php</h1>
    
    <div id="result"></div>
    
    <script>
    // Тестовый запрос
    const formData = new FormData();
    formData.append('id', '1');
    formData.append('site_title', 'Тест');
    formData.append('hero_title', 'Тест');
    formData.append('hero_subtitle', 'Тест');
    
    fetch(\'save_settings.php\', {
        method: \'POST\',
        body: formData,
        credentials: \'same-origin\'
    })
    .then(response => {
        console.log(\'Status:\', response.status);
        console.log(\'Headers:\', [...response.headers]);
        console.log(\'Content-Type:\', response.headers.get(\'content-type\'));
        
        return response.text();
    })
    .then(text => {
        console.log(\'Response text:\', text);
        document.getElementById(\'result\').innerHTML = \'<h2>Ответ сервера:</h2><pre>\' + 
            text.replace(/</g, \'&lt;\').replace(/>/g, \'&gt;\') + \'</pre>\';
    })
    .catch(error => {
        console.error(\'Error:\', error);
        document.getElementById(\'result\').innerHTML = \'<div style="color: red">Ошибка: \' + error.message + \'</div>\';
    });
    </script>
    
    <br><br>
    <a href="index.php">Вернуться в админ-панель</a>
</body>
</html>';
?>