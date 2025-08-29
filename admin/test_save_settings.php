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
    <title>Тест сохранения настроек</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .test-result {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin: 15px 0;
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
                    <a class="nav-link active" href="test_save_settings.php">
                        <i class="fas fa-vial"></i> Тест сохранения
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="fix_about_text.php">
                        <i class="fas fa-tools"></i> Исправление "Обо мне"
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sql_executor.php">
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
                <h2>Тест сохранения настроек</h2>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Информация:</strong> Эта страница поможет протестировать работу сохранения настроек и выявить возможные проблемы.
            </div>

            <!-- Тест формы -->
            <div class="card">
                <div class="card-header">
                    <h5>Тестовая форма сохранения настроек</h5>
                </div>
                <div class="card-body">
                    <form id="testSettingsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Название сайта</label>
                                    <input type="text" class="form-control" name="site_title" value="Тестовый сайт" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Заголовок героя</label>
                                    <input type="text" class="form-control" name="hero_title" value="Тестовый герой" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Подзаголовок героя</label>
                                    <input type="text" class="form-control" name="hero_subtitle" value="Тестовый подзаголовок" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Обо мне</label>
                                    <textarea class="form-control" name="about_text" rows="4" placeholder="Это тестовый текст для проверки сохранения поля about_text...">Это тестовый текст для проверки сохранения поля about_text. Создан <?php echo date('Y-m-d H:i:s'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Лет опыта</label>
                                    <input type="number" class="form-control" name="experience_years" value="5">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Проектов выполнено</label>
                                    <input type="number" class="form-control" name="projects_count" value="100">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Клиентов</label>
                                    <input type="number" class="form-control" name="clients_count" value="50">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ID</label>
                                    <input type="number" class="form-control" name="id" value="1" readonly>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="testBtn">
                            <i class="fas fa-play"></i> Тестировать сохранение
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearResult()">
                            <i class="fas fa-eraser"></i> Очистить
                        </button>
                    </form>
                </div>
            </div>

            <!-- Результат теста -->
            <div id="resultContainer" style="display: none;">
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Результат теста</h5>
                    </div>
                    <div class="card-body">
                        <div id="resultContent"></div>
                    </div>
                </div>
            </div>

            <!-- Информация о текущих настройках -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Текущие настройки в базе данных</h5>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT * FROM settings LIMIT 1";
                    $result = $conn->query($sql);
                    
                    if ($result && $row = $result->fetch_assoc()) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped">';
                        echo '<tbody>';
                        
                        foreach ($row as $key => $value) {
                            $is_about_text = ($key === 'about_text');
                            $value_display = $value;
                            
                            if ($is_about_text && empty($value)) {
                                $value_display = '<em class="text-muted">(пусто)</em>';
                            } elseif (strlen($value) > 100) {
                                $value_display = substr($value, 0, 100) . '...';
                            }
                            
                            echo '<tr>';
                            echo '<td width="30%"><strong>' . htmlspecialchars($key) . '</strong></td>';
                            echo '<td>' . $value_display . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    } else {
                        echo '<div class="error-box">';
                        echo '<i class="fas fa-exclamation-triangle"></i> Ошибка при получении данных: ' . htmlspecialchars($conn->error);
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Проверка логов -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Последние записи в логе</h5>
                </div>
                <div class="card-body">
                    <?php
                    $logFile = __DIR__ . '/logs/settings.log';
                    if (file_exists($logFile)) {
                        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                        if ($logs) {
                            $lastLogs = array_slice($logs, -10); // Последние 10 строк
                            echo '<div class="code-block">';
                            foreach ($lastLogs as $log) {
                                echo htmlspecialchars($log) . "\n";
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info">Лог файл пустой</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Лог файл не найден: ' . htmlspecialchars($logFile) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Функция для очистки результата
        function clearResult() {
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('resultContent').innerHTML = '';
        }

        // Обработка формы
        document.getElementById('testSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const testBtn = document.getElementById('testBtn');
            const originalText = testBtn.innerHTML;
            
            // Показываем индикатор загрузки
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Тестирование...';
            
            const formData = new FormData(this);
            
            fetch('save_settings.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers]);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Проверяем Content-Type
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error(`Ожидаемый JSON, получен ${contentType}`);
                }
                
                return response.text().then(text => {
                    console.log('Response text:', text);
                    
                    // Проверяем, что ответ не начинается с HTML
                    if (text.trim().startsWith('<')) {
                        throw new Error(`Сервер вернул HTML вместо JSON: ${text.substring(0, 100)}...`);
                    }
                    
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error(`Невалидный JSON: ${text.substring(0, 200)}...`);
                    }
                });
            })
            .then(data => {
                console.log('Parsed data:', data);
                
                const resultContainer = document.getElementById('resultContainer');
                const resultContent = document.getElementById('resultContent');
                
                resultContainer.style.display = 'block';
                
                if (data.success) {
                    resultContent.innerHTML = '<div class="success-box">';
                    resultContent.innerHTML += '<i class="fas fa-check-circle"></i> ' + htmlspecialchars(data.message);
                    resultContent.innerHTML += '<br><small>Затронуто строк: ' + (data.affected_rows || 0) + '</small>';
                    resultContent.innerHTML += '</div>';
                    
                    // Перезагружаем страницу через 2 секунды
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    resultContent.innerHTML = '<div class="error-box">';
                    resultContent.innerHTML += '<i class="fas fa-exclamation-circle"></i> Ошибка: ' + htmlspecialchars(data.message);
                    resultContent.innerHTML += '</div>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                
                const resultContainer = document.getElementById('resultContainer');
                const resultContent = document.getElementById('resultContent');
                
                resultContainer.style.display = 'block';
                resultContent.innerHTML = '<div class="error-box">';
                resultContent.innerHTML += '<i class="fas fa-exclamation-circle"></i> Ошибка запроса: ' + htmlspecialchars(error.message);
                resultContent.innerHTML += '</div>';
            })
            .finally(() => {
                testBtn.disabled = false;
                testBtn.innerHTML = originalText;
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