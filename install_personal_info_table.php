<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Установка таблицы персональной информации</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header'>
                        <h2><i class='fas fa-database'></i> Установка таблицы персональной информации</h2>
                    </div>
                    <div class='card-body'>
";

try {
    // Читаем SQL файл
    $sql = file_get_contents('create_personal_info_table.sql');
    
    // Разделяем на отдельные запросы
    $queries = explode(';', $sql);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query)) {
                echo "<div class='alert alert-success'>
                    <i class='fas fa-check-circle'></i> Запрос выполнен успешно
                </div>";
                $success_count++;
            } else {
                $error = $conn->error;
                // Игнорируем ошибки о несуществующих колонок (это нормально при повторном запуске)
                if (strpos($error, "Unknown column") === false && strpos($error, "DROP COLUMN") === false) {
                    echo "<div class='alert alert-danger'>
                        <i class='fas fa-exclamation-circle'></i> Ошибка выполнения запроса: " . htmlspecialchars($error) . "
                    </div>";
                    echo "<div class='alert alert-secondary'>
                        <strong>Запрос:</strong><br>
                        <code>" . htmlspecialchars($query) . "</code>
                    </div>";
                    $error_count++;
                } else {
                    echo "<div class='alert alert-warning'>
                        <i class='fas fa-info-circle'></i> Предупреждение: " . htmlspecialchars($error) . "
                    </div>";
                }
            }
        }
    }
    
    if ($error_count === 0) {
        echo "<div class='alert alert-success'>
            <h4><i class='fas fa-check-circle'></i> Установка успешно завершена!</h4>
            <p>Выполнено запросов: $success_count</p>
        </div>";
        
        echo "<div class='d-grid gap-2'>
            <a href='admin/manage_personal_info.php' class='btn btn-primary'>
                <i class='fas fa-user-edit'></i> Перейти к управлению персональной информацией
            </a>
            <a href='admin/' class='btn btn-secondary'>
                <i class='fas fa-tachometer-alt'></i> Перейти в админ-панель
            </a>
            <a href='check_personal_info_install.php' class='btn btn-info'>
                <i class='fas fa-check'></i> Проверить установку
            </a>
        </div>";
    } else {
        echo "<div class='alert alert-danger'>
            <h4><i class='fas fa-exclamation-triangle'></i> Установка завершена с ошибками</h4>
            <p>Успешных запросов: $success_count</p>
            <p>Ошибок: $error_count</p>
        </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
        <h4><i class='fas fa-exclamation-triangle'></i> Критическая ошибка</h4>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
    </div>";
}

echo "
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>