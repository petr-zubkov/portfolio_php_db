<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die('Доступ запрещен');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Минимальный тест</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        #result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Минимальный тест сохранения</h1>
    
    <form id="minimalForm">
        <div class="form-group">
            <label>Название сайта:</label>
            <input type="text" name="site_title" value="Минимальный тест" required>
        </div>
        
        <input type="hidden" name="id" value="1">
        
        <button type="submit">Сохранить</button>
    </form>
    
    <div id="result"></div>
    
    <script>
    document.getElementById("minimalForm").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById("result");
        
        resultDiv.innerHTML = "<div>Отправка...</div>";
        
        fetch("save_settings_minimal.php", {
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
            console.log("Response:", text);
            
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    resultDiv.innerHTML = `<div class="success">✓ ${data.message}</div>`;
                } else {
                    resultDiv.innerHTML = `<div class="error">✗ ${data.message}</div>`;
                }
            } catch (e) {
                resultDiv.innerHTML = `<div class="error">✗ JSON Error: ${e.message}</div>`;
                resultDiv.innerHTML += `<div>Response: ${text}</div>`;
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