<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Проверка консоли браузера</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .instructions { 
            background: #e7f3ff; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0; 
        }
        .test-area { 
            margin: 20px 0; 
            padding: 20px; 
            border: 2px dashed #ccc; 
            border-radius: 8px; 
        }
        button { 
            margin: 10px; 
            padding: 10px 20px; 
            background: #28a745; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <h1>Проверка консоли браузера</h1>
    
    <div class="instructions">
        <h3>Инструкции:</h3>
        <ol>
            <li>Нажмите F12 на клавиатуре или правой кнопкой мыши → "Просмотреть код"</li>
            <li>Перейдите на вкладку "Console" (Консоль)</li>
            <li>Нажмите на кнопки ниже и проверьте, появляются ли сообщения в консоли</li>
            <li>Если сообщения не появляются - проблема с JavaScript</li>
        </ol>
    </div>
    
    <div class="test-area">
        <h3>Тестовая область:</h3>
        <button onclick="test1()">Тест 1 - Простой клик</button>
        <button onclick="test2()">Тест 2 - С данными</button>
        <button onclick="test3()">Тест 3 - Fetch запрос</button>
        <button onclick="test4()">Тест 4 - Ошибка</button>
    </div>
    
    <script>
    console.log("=== Начало тестирования ===");
    console.log("Страница загружена");
    console.log("Время загрузки:", new Date().toLocaleTimeString());
    
    function test1() {
        console.log("Тест 1: Простой клик выполнен");
        console.trace("Trace для теста 1");
        alert("Тест 1 выполнен! Проверьте консоль.");
    }
    
    function test2() {
        const data = {
            name: "Тестовые данные",
            number: 42,
            array: [1, 2, 3],
            date: new Date()
        };
        console.log("Тест 2: Данные:", data);
        console.table(data);
        alert("Тест 2 выполнен! Проверьте консоль.");
    }
    
    function test3() {
        console.log("Тест 3: Начинаем fetch запрос");
        
        fetch("save_settings.php", {
            method: "POST",
            body: new FormData()
        })
        .then(response => {
            console.log("Response status:", response.status);
            console.log("Response headers:", response.headers);
            return response.text();
        })
        .then(text => {
            console.log("Response text:", text);
            alert("Тест 3 выполнен! Проверьте консоль.");
        })
        .catch(error => {
            console.error("Тест 3 ошибка:", error);
            alert("Тест 3 выполнен с ошибкой! Проверьте консоль.");
        });
    }
    
    function test4() {
        try {
            undefinedFunction(); // Вызовет ошибку
        } catch (error) {
            console.error("Тест 4: Перехваченная ошибка:", error.message);
            alert("Тест 4 выполнен! Проверьте консоль.");
        }
    }
    
    // Проверяем события
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOM загружен");
        
        document.querySelectorAll("button").forEach((button, index) => {
            console.log("Кнопка", index + 1, ":", button.textContent);
        });
    });
    
    window.addEventListener("load", function() {
        console.log("Окно полностью загружено");
    });
    
    window.addEventListener("error", function(e) {
        console.error("Глобальная ошибка:", e.message);
    });
    
    console.log("=== Скрипт загружен ===");
    </script>
    
    <br><br>
    <a href="debug_admin.php">Вернуться к отладочной админ-панели</a>
    <br>
    <a href="index.php">Вернуться к основной админ-панели</a>
</body>
</html>';
?>