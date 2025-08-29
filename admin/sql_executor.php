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
    <title>SQL Executor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .sql-result {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
        }
        .sql-query {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            min-height: 120px;
            resize: vertical;
        }
        .result-table {
            font-size: 0.9em;
        }
        .result-table th {
            background: #e9ecef;
            position: sticky;
            top: 0;
        }
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .query-examples {
            background: #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .example-query {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 3px;
            padding: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .example-query:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Сайдбар -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Админ-панель</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="sql_executor.php">
                        <i class="fas fa-database"></i> SQL Executor
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_logs.php">
                        <i class="fas fa-file-alt"></i> Логи ошибок
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Выход
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="admin-main">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>SQL Executor</h2>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Внимание!</strong> Будьте осторожны при выполнении SQL-запросов. Операции DROP, TRUNCATE, ALTER, CREATE, GRANT, REVOKE запрещены для безопасности.
            </div>

            <!-- Примеры запросов -->
            <div class="query-examples">
                <h5>Примеры запросов:</h5>
                <div class="example-query" onclick="setQuery(this)">
                    SELECT * FROM settings LIMIT 1
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    SELECT * FROM projects ORDER BY created_at DESC
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    SELECT * FROM skills
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    SELECT * FROM contact LIMIT 1
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    UPDATE settings SET about_text = 'Тестовый текст обо мне' WHERE id = 1
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    SHOW TABLES
                </div>
                <div class="example-query" onclick="setQuery(this)">
                    DESCRIBE settings
                </div>
            </div>

            <!-- Форма выполнения запроса -->
            <div class="card">
                <div class="card-header">
                    <h5>Выполнить SQL-запрос</h5>
                </div>
                <div class="card-body">
                    <form id="sqlForm">
                        <div class="mb-3">
                            <label class="form-label">SQL-запрос:</label>
                            <textarea class="form-control sql-query" id="sqlQuery" name="sql_query" 
                                placeholder="Введите ваш SQL-запрос здесь..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="executeBtn">
                            <i class="fas fa-play"></i> Выполнить
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearResult()">
                            <i class="fas fa-eraser"></i> Очистить
                        </button>
                    </form>
                </div>
            </div>

            <!-- Результат выполнения -->
            <div id="resultContainer" style="display: none;">
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Результат выполнения</h5>
                    </div>
                    <div class="card-body">
                        <div id="resultContent"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Функция для установки примера запроса
        function setQuery(element) {
            document.getElementById('sqlQuery').value = element.textContent.trim();
        }

        // Функция для очистки результата
        function clearResult() {
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('resultContent').innerHTML = '';
            document.getElementById('sqlQuery').value = '';
        }

        // Обработка формы
        document.getElementById('sqlForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const executeBtn = document.getElementById('executeBtn');
            const originalText = executeBtn.innerHTML;
            
            // Показываем индикатор загрузки
            executeBtn.disabled = true;
            executeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Выполнение...';
            
            const formData = new FormData(this);
            
            fetch('execute_sql.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text().then(text => {
                    console.log('Response text:', text);
                    
                    if (text.trim().startsWith('<')) {
                        throw new Error(`Сервер вернул HTML вместо JSON`);
                    }
                    
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error(`Невалидный JSON`);
                    }
                });
            })
            .then(data => {
                console.log('Parsed data:', data);
                
                const resultContainer = document.getElementById('resultContainer');
                const resultContent = document.getElementById('resultContent');
                
                resultContainer.style.display = 'block';
                
                if (data.success) {
                    let html = '<div class="success-message">';
                    html += '<i class="fas fa-check-circle"></i> ' + data.message;
                    html += '<br><small>Тип запроса: ' + data.query_type + ', Затронуто строк: ' + data.rows_affected;
                    if (data.insert_id) {
                        html += ', ID вставки: ' + data.insert_id;
                    }
                    html += '</small></div>';
                    
                    // Если есть данные (SELECT), выводим таблицу
                    if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                        html += '<div class="table-responsive">';
                        html += '<table class="table table-striped result-table">';
                        
                        // Заголовки таблицы
                        html += '<thead><tr>';
                        Object.keys(data.data[0]).forEach(key => {
                            html += '<th>' + htmlspecialchars(key) + '</th>';
                        });
                        html += '</tr></thead>';
                        
                        // Данные таблицы
                        html += '<tbody>';
                        data.data.forEach(row => {
                            html += '<tr>';
                            Object.values(row).forEach(value => {
                                html += '<td>' + htmlspecialchars(value) + '</td>';
                            });
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    } else if (data.data && Array.isArray(data.data) && data.data.length === 0) {
                        html += '<div class="alert alert-info">Запрос выполнен успешно, но не вернул данных.</div>';
                    }
                    
                    resultContent.innerHTML = html;
                } else {
                    resultContent.innerHTML = '<div class="error-message">';
                    resultContent.innerHTML += '<i class="fas fa-exclamation-circle"></i> ' + htmlspecialchars(data.message);
                    resultContent.innerHTML += '</div>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                
                const resultContainer = document.getElementById('resultContainer');
                const resultContent = document.getElementById('resultContent');
                
                resultContainer.style.display = 'block';
                resultContent.innerHTML = '<div class="error-message">';
                resultContent.innerHTML += '<i class="fas fa-exclamation-circle"></i> Ошибка запроса: ' + htmlspecialchars(error.message);
                resultContent.innerHTML += '</div>';
            })
            .finally(() => {
                executeBtn.disabled = false;
                executeBtn.innerHTML = originalText;
            });
        });

        // Функция для экранирования HTML
        function htmlspecialchars(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
</body>
</html>