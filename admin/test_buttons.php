<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Тест кнопок</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-button { 
            margin: 10px; 
            padding: 10px 20px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .test-button:hover { background: #0056b3; }
        .log { 
            margin: 20px 0; 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 4px; 
            font-family: monospace; 
        }
    </style>
</head>
<body>
    <h1>Тестирование кнопок</h1>
    
    <button class="test-button" onclick="testClick(this)">Тестовая кнопка 1</button>
    <button class="test-button" onclick="testClick(this)">Тестовая кнопка 2</button>
    <button class="test-button" onclick="testClick(this)">Тестовая кнопка 3</button>
    
    <div id="log" class="log">Ждем кликов по кнопкам...</div>
    
    <script>
    function testClick(button) {
        const log = document.getElementById("log");
        const timestamp = new Date().toLocaleTimeString();
        log.innerHTML += "[" + timestamp + "] Клик по кнопке: " + button.textContent + "\\n";
        console.log("Клик по кнопке:", button.textContent);
        
        // Показываем уведомление
        alert("Кнопка работает! " + button.textContent);
    }
    
    // Проверяем загрузку DOM
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOM загружен");
        document.getElementById("log").innerHTML += "[DOM] Страница загружена\\n";
        
        // Проверяем все кнопки
        const buttons = document.querySelectorAll(".test-button");
        buttons.forEach((button, index) => {
            console.log("Найдена кнопка", index + 1, ":", button.textContent);
            document.getElementById("log").innerHTML += "[DOM] Найдена кнопка: " + button.textContent + "\\n";
        });
    });
    
    console.log("Скрипт загружен");
    </script>
    
    <br><br>
    <a href="debug_admin.php">Вернуться к отладочной админ-панели</a>
    <br>
    <a href="index.php">Вернуться к основной админ-панели</a>
</body>
</html>';
?>