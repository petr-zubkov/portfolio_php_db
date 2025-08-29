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
    <title>Исправление проблемы с "Обо мне"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .fix-result {
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
        .step-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .step-number {
            background: #007bff;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
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
                    <a class="nav-link active" href="fix_about_text.php">
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
                <h2>Исправление проблемы с сохранением "Обо мне"</h2>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Информация:</strong> Эта страница поможет диагностировать и исправить проблему с сохранением текста "Обо мне" в базе данных.
            </div>

            <!-- Шаг 1: Проверка базы данных -->
            <div class="step-card">
                <h4><span class="step-number">1</span>Проверка структуры базы данных</h4>
                <p>Проверяем наличие поля <code>about_text</code> в таблице <code>settings</code>:</p>
                
                <?php
                $check_sql = "DESCRIBE settings";
                $result = $conn->query($check_sql);
                
                if ($result) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Поле</th><th>Тип</th><th>Null</th><th>Ключ</th><th>По умолчанию</th></tr></thead>';
                    echo '<tbody>';
                    
                    $about_text_exists = false;
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td><strong>' . htmlspecialchars($row['Field']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Default'] ?? '') . '</td>';
                        echo '</tr>';
                        
                        if ($row['Field'] === 'about_text') {
                            $about_text_exists = true;
                        }
                    }
                    echo '</tbody></table></div>';
                    
                    if ($about_text_exists) {
                        echo '<div class="success-box">';
                        echo '<i class="fas fa-check-circle"></i> Поле <code>about_text</code> существует в таблице settings';
                        echo '</div>';
                    } else {
                        echo '<div class="error-box">';
                        echo '<i class="fas fa-exclamation-triangle"></i> Поле <code>about_text</code> отсутствует в таблице settings';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="error-box">';
                    echo '<i class="fas fa-exclamation-triangle"></i> Ошибка при проверке структуры: ' . htmlspecialchars($conn->error);
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Шаг 2: Проверка текущих данных -->
            <div class="step-card">
                <h4><span class="step-number">2</span>Проверка текущих данных</h4>
                <p>Текущие данные в таблице settings:</p>
                
                <?php
                $data_sql = "SELECT * FROM settings LIMIT 1";
                $result = $conn->query($data_sql);
                
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
                    
                    if (empty($row['about_text'])) {
                        echo '<div class="alert alert-warning">';
                        echo '<i class="fas fa-exclamation-triangle"></i> Поле <code>about_text</code> пустое. Это может быть причиной проблемы.';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="error-box">';
                    echo '<i class="fas fa-exclamation-triangle"></i> Ошибка при получении данных: ' . htmlspecialchars($conn->error);
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Шаг 3: Тестовое обновление -->
            <div class="step-card">
                <h4><span class="step-number">3</span>Тестовое обновление поля about_text</h4>
                <p>Проверим, можно ли обновить поле about_text напрямую:</p>
                
                <?php
                $test_text = "Это тестовый текст для проверки работы поля about_text. Создан " . date('Y-m-d H:i:s');
                $update_sql = "UPDATE settings SET about_text = ? WHERE id = 1";
                
                $stmt = $conn->prepare($update_sql);
                if ($stmt) {
                    $stmt->bind_param("s", $test_text);
                    if ($stmt->execute()) {
                        $affected = $stmt->affected_rows;
                        echo '<div class="success-box">';
                        echo '<i class="fas fa-check-circle"></i> Тестовое обновление выполнено успешно. Затронуто строк: ' . $affected;
                        echo '</div>';
                        
                        // Проверяем, что данные сохранились
                        $check_sql = "SELECT about_text FROM settings WHERE id = 1";
                        $result = $conn->query($check_sql);
                        if ($result && $row = $result->fetch_assoc()) {
                            echo '<div class="alert alert-info">';
                            echo '<i class="fas fa-info-circle"></i> Сохраненное значение: ' . htmlspecialchars($row['about_text']);
                            echo '</div>';
                        }
                        
                    } else {
                        echo '<div class="error-box">';
                        echo '<i class="fas fa-exclamation-triangle"></i> Ошибка при выполнении тестового обновления: ' . htmlspecialchars($stmt->error);
                        echo '</div>';
                    }
                    $stmt->close();
                } else {
                    echo '<div class="error-box">';
                    echo '<i class="fas fa-exclamation-triangle"></i> Ошибка при подготовке запроса: ' . htmlspecialchars($conn->error);
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Шаг 4: Рекомендации -->
            <div class="step-card">
                <h4><span class="step-number">4</span>Рекомендации по исправлению</h4>
                
                <div class="alert alert-success">
                    <h5><i class="fas fa-lightbulb"></i> Проблема найдена и решена!</h5>
                    <p>Основная проблема была в файле <code>admin/save_settings.php</code> - поле <code>about_text</code> не включалось в SQL-запрос обновления.</p>
                </div>
                
                <h5>Что нужно сделать:</h5>
                <ol>
                    <li><strong>Заменить файл обработчика:</strong> Замените <code>admin/save_settings.php</code> на исправленную версию <code>admin/save_settings_fixed.php</code></li>
                    <li><strong>Обновить JavaScript:</strong> Убедитесь, что в <code>assets/js/admin.js</code> указан правильный путь к обработчику</li>
                    <li><strong>Протестировать:</strong> Попробуйте снова сохранить текст "Обо мне" через админ-панель</li>
                </ol>
                
                <h5>Быстрое исправление:</h5>
                <div class="code-block">
                    // В файле admin/save_settings.php измените SQL-запрос:<br>
                    $sql = "UPDATE settings SET <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;site_title = ?, <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;hero_title = ?, <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;hero_subtitle = ?,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>about_text = ?,</strong><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;experience_years = ?,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;projects_count = ?,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;clients_count = ?<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;WHERE id = ?";<br><br>
                    
                    // И измените bind_param:<br>
                    $stmt->bind_param("<strong>ssss</strong>iiii", ...);
                </div>
                
                <div class="mt-3">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Вернуться в админ-панель и протестировать
                    </a>
                    <a href="sql_executor.php" class="btn btn-secondary">
                        <i class="fas fa-database"></i> Открыть SQL Executor
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>