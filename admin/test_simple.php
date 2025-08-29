<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

require_once '../config.php';

// Устанавливаем заголовки
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Простой тест сохранения</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Простой тест сохранения настроек</h1>
    
    <div class="card">
        <div class="card-body">
            <form id="testForm">
                <div class="mb-3">
                    <label class="form-label">Название сайта</label>
                    <input type="text" class="form-control" name="site_title" value="Тестовый сайт" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Обо мне</label>
                    <textarea class="form-control" name="about_text" rows="4">Это тестовый текст для проверки сохранения поля about_text. Создан <?php echo date('Y-m-d H:i:s'); ?></textarea>
                </div>
                <input type="hidden" name="id" value="1">
                <input type="hidden" name="hero_title" value="Тестовый герой">
                <input type="hidden" name="hero_subtitle" value="Тестовый подзаголовок">
                <input type="hidden" name="experience_years" value="5">
                <input type="hidden" name="projects_count" value="100">
                <input type="hidden" name="clients_count" value="50">
                <button type="submit" class="btn btn-primary">Тестировать сохранение</button>
            </form>
        </div>
    </div>
    
    <div id="result"></div>
    
    <script>
    document.getElementById('testForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Тестирование...';
        
        const formData = new FormData(this);
        
        fetch('save_settings.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
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
                resultDiv.innerHTML = '<div class="result success"><h4>✅ Успех!</h4><p>' + data.message + '</p><small>Затронуто строк: ' + (data.affected_rows || 0) + '</small></div>';
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