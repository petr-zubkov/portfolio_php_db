<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

$logType = $_GET['type'] ?? 'settings';
$logFile = __DIR__ . '/logs/' . $logType . '.log';

echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи - ' . htmlspecialchars($logType) . '</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .log-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            max-height: 600px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        .log-entry {
            margin-bottom: 0.5rem;
            padding: 0.25rem;
            border-radius: 0.25rem;
            white-space: pre-wrap;
        }
        .log-entry.success { background-color: #d4edda; color: #155724; }
        .log-entry.error { background-color: #f8d7da; color: #721c24; }
        .log-entry.info { background-color: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <h1><i class="fas fa-file-alt"></i> Логи - ' . htmlspecialchars($logType) . '</h1>
                    <div>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                        <a href="clear_logs.php?type=' . htmlspecialchars($logType) . '" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Очистить
                        </a>
                        <a href="test_final.php" class="btn btn-primary">
                            <i class="fas fa-flask"></i> Тест
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Последние записи лога</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="log-container">';

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (empty($logs)) {
        echo '<div class="text-center text-muted py-4">Лог-файл пуст</div>';
    } else {
        $logEntries = explode("\n", trim($logs));
        foreach ($logEntries as $entry) {
            if (empty($entry)) continue;
            
            $class = 'info';
            if (strpos($entry, 'УСПЕХ:') !== false || strpos($entry, 'Успешный ответ') !== false) {
                $class = 'success';
            } elseif (strpos($entry, 'ОШИБКА:') !== false || strpos($entry, 'Фатальная ошибка') !== false) {
                $class = 'error';
            }
            
            echo '<div class="log-entry ' . $class . '">' . htmlspecialchars($entry) . '</div>';
        }
    }
} else {
    echo '<div class="text-center text-muted py-4">Лог-файл не найден</div>';
}

echo '</div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Информация о лог-файле:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Путь:</strong> ' . $logFile . '</li>
                        <li><strong>Размер:</strong> ' . (file_exists($logFile) ? filesize($logFile) . ' байт' : 'Файл не существует') . '</li>
                        <li><strong>Последнее изменение:</strong> ' . (file_exists($logFile) ? date('Y-m-d H:i:s', filemtime($logFile)) : 'Файл не существует') . '</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
?>