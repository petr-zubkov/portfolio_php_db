<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Финальный тест</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; max-width: 400px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        #result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Финальный тест сохранения настроек</h1>
    
    <form id="finalForm">
        <div class="form-group">
            <label>Название сайта:</label>
            <input type="text" name="site_title" value="Финальный тест" required>
        </div>
        
        <div class="form-group">
            <label>Заголовок героя:</label>
            <input type="text" name="hero_title" value="Тестовый герой" required>
        </div>
        
        <div class="form-group">
            <label>Подзаголовок героя:</label>
            <input type="text" name="hero_subtitle" value="Тестовый подзаголовок">
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
        
        <button type="submit">Отправить в save_settings_final.php</button>
        <button type="button" onclick="testOriginal()">Отправить в оригинальный save_settings.php</button>
        <button type="button" onclick="clearLogs()">Очистить логи</button>
    </form>
    
    <div id="result"></div>
    
    <div id="logs" style="margin-top: 30px;">
        <h3>Последние записи лога:</h3>
        <div id="logContent"></div>
        <button onclick="loadLogs()">Обновить логи</button>
    </div>
    
    <script>
    document.getElementById("finalForm").addEventListener("submit", function(e) {
        e.preventDefault();
        sendRequest("save_settings_final.php");
    });
    
    function sendRequest(url) {
        const formData = new FormData(document.getElementById("finalForm"));
        const resultDiv = document.getElementById("result");
        
        resultDiv.innerHTML = "<div class=\"info\">Отправка запроса в " + url + "...</div>";
        
        fetch(url, {
            method: "POST",
            body: formData
        })
        .then(response => {
            console.log("Status:", response.status);
            console.log("Headers:", [...response.headers]);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
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
                resultDiv.innerHTML = `<div class="error">✗ JSON Error: ${e.message}</div>`;
                resultDiv.innerHTML += `<div>Response: <pre>${text}</pre></div>`;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            resultDiv.innerHTML = `<div class="error">✗ ${error.message}</div>`;
        })
        .finally(() => {
            loadLogs(); // Обновляем логи после запроса
        });
    }
    
    function testOriginal() {
        sendRequest("save_settings.php");
    }
    
    function loadLogs() {
        fetch("view_logs.php?type=debug")
        .then(response => response.text())
        .then(html => {
            // Извлекаем логи из HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const logEntries = doc.querySelectorAll(".log-entry");
            
            const logContent = document.getElementById("logContent");
            logContent.innerHTML = "";
            
            // Показываем последние 10 записей
            const recentLogs = Array.from(logEntries).slice(-10);
            recentLogs.forEach(entry => {
                const clone = entry.cloneNode(true);
                logContent.appendChild(clone);
            });
            
            if (recentLogs.length === 0) {
                logContent.innerHTML = "<div class=\"info\">Логи пусты</div>";
            }
        })
        .catch(error => {
            document.getElementById("logContent").innerHTML = "<div class=\"error\">Ошибка загрузки логов</div>";
        });
    }
    
    function clearLogs() {
        if (confirm("Вы уверены, что хотите очистить логи?")) {
            fetch("clear_logs.php?type=debug")
            .then(() => {
                document.getElementById("logContent").innerHTML = "<div class=\"info\">Логи очищены</div>";
            });
        }
    }
    
    // Загружаем логи при загрузке страницы
    loadLogs();
    </script>
    
    <br><br>
    <a href="index.php">Вернуться в админ-панель</a>
</body></html>';
?>