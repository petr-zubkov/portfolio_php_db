<?php
// Устанавливаем заголовки
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Минимальный тест формы</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Минимальный тест формы</h1>
    
    <form id="testForm">
        <div class="form-group">
            <label>Название сайта</label>
            <input type="text" name="site_title" value="Тестовый сайт" required>
        </div>
        <div class="form-group">
            <label>Обо мне</label>
            <textarea name="about_text" rows="4">Это тестовый текст для проверки сохранения поля about_text. Создан <?php echo date('Y-m-d H:i:s'); ?></textarea>
        </div>
        <div class="form-group">
            <label>ID</label>
            <input type="number" name="id" value="1">
        </div>
        <button type="submit">Отправить тест</button>
    </form>
    
    <div id="result"></div>
    
    <script>
    document.getElementById('testForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const button = this.querySelector('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Отправка...';
        
        const formData = new FormData(this);
        
        fetch('test_save_minimal.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            const resultDiv = document.getElementById('result');
            if (data.success) {
                resultDiv.innerHTML = '<div class="result success"><h4>✅ Успех!</h4><p>' + data.message + '</p><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
            } else {
                resultDiv.innerHTML = '<div class="result error"><h4>❌ Ошибка!</h4><p>' + data.message + '</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = '<div class="result error"><h4>❌ Ошибка запроса!</h4><p>' + error.message + '</p></div>';
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    });
    </script>
</body>
</html>