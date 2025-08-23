<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

require_once '../config.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Тест формы настроек</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        #result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Тест формы настроек</h1>
    
    <form id="testForm">
        <div class="form-group">
            <label>Название сайта:</label>
            <input type="text" name="site_title" value="Тестовый сайт" required>
        </div>
        
        <div class="form-group">
            <label>Заголовок героя:</label>
            <input type="text" name="hero_title" value="Тестовый герой" required>
        </div>
        
        <div class="form-group">
            <label>Подзаголовок героя:</label>
            <input type="text" name="hero_subtitle" value="Тестовый подзаголовок" required>
        </div>
        
        <div class="form-group">
            <label>Лет опыта:</label>
            <input type="number" name="experience_years" value="5">
        </div>
        
        <div class="form-group">
            <label>Проектов выполнено:</label>
            <input type="number" name="projects_count" value="100">
        </div>
        
        <div class="form-group">
            <label>Клиентов:</label>
            <input type="number" name="clients_count" value="50">
        </div>
        
        <input type="hidden" name="id" value="1">
        
        <button type="submit">Отправить тестовый запрос</button>
    </form>
    
    <div id="result"></div>
    
    <script>
    document.getElementById("testForm").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById("result");
        
        // Показываем отправку
        resultDiv.innerHTML = "<div class=\"info\">Отправка запроса...</div>";
        
        fetch("save_settings.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        })
        .then(response => {
            console.log("Status:", response.status);
            console.log("Headers:", [...response.headers]);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text();
        })
        .then(text => {
            console.log("Response text:", text);
            
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    resultDiv.innerHTML = `<div class="success">✓ ${data.message}</div>`;
                    if (data.affected_rows !== undefined) {
                        resultDiv.innerHTML += `<div class="success">Затронуто строк: ${data.affected_rows}</div>`;
                    }
                } else {
                    resultDiv.innerHTML = `<div class="error">✗ ${data.message}</div>`;
                }
            } catch (e) {
                resultDiv.innerHTML = `<div class="error">✗ Ошибка парсинга JSON: ${e.message}</div>`;
                resultDiv.innerHTML += `<div class="error">Ответ сервера: <pre>${text}</pre></div>`;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            resultDiv.innerHTML = `<div class="error">✗ ${error.message}</div>`;
        });
    });
    </script>
    
    <br><br>
    <a href="index.php">Вернуться в админ-панель</a>
</body>
</html>';
?>