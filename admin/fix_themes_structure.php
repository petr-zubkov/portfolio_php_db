<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Функция для выполнения SQL-запросов
function executeSQL($conn, $sql) {
    $result = $conn->query($sql);
    if ($result === false) {
        throw new Exception("Ошибка выполнения SQL: " . $conn->error);
    }
    return $result;
}

// Функция для проверки существования колонки
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result->num_rows > 0;
}

// Функция для безопасного вывода
function safeEcho($text) {
    echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Функция для логирования
function logMessage($message) {
    $logFile = __DIR__ . '/logs/fix_themes_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Создаем директорию для логов, если ее нет
        if (!file_exists(__DIR__ . '/logs')) {
            mkdir(__DIR__ . '/logs', 0755, true);
        }
        
        $results = [];
        $errors = [];
        
        // Начинаем транзакцию
        $conn->begin_transaction();
        
        logMessage("Начало выполнения скрипта очистки таблицы themes");
        
        // Шаг 1: Проверяем текущую структуру таблицы themes
        logMessage("Проверка текущей структуры таблицы themes");
        $columnsResult = $conn->query("SHOW COLUMNS FROM themes");
        $existingColumns = [];
        while ($row = $columnsResult->fetch_assoc()) {
            $existingColumns[] = $row['Field'];
        }
        $results[] = "Текущие колонки в таблице themes: " . implode(', ', $existingColumns);
        
        // Шаг 2: Удаляем колонки с личной информацией (если они существуют)
        $personalColumns = [
            'site_title', 'hero_title', 'hero_subtitle', 'avatar', 
            'about_text', 'experience_years', 'projects_count', 'clients_count'
        ];
        
        foreach ($personalColumns as $column) {
            if (columnExists($conn, 'themes', $column)) {
                $sql = "ALTER TABLE themes DROP COLUMN `$column`";
                executeSQL($conn, $sql);
                $results[] = "✓ Удалена колонка: $column";
                logMessage("Удалена колонка: $column");
            } else {
                $results[] = "- Колонка $column не существует (пропускаем)";
            }
        }
        
        // Шаг 3: Проверяем наличие данных в таблице settings
        logMessage("Проверка данных в таблице settings");
        $settingsResult = $conn->query("SELECT * FROM settings LIMIT 1");
        if ($settingsResult->num_rows === 0) {
            // Вставляем базовые настройки, если таблица пуста
            $defaultSettings = [
                'site_title' => 'Портфолио верстальщика книг',
                'hero_title' => 'Верстальщик книг',
                'hero_subtitle' => 'Профессиональная верстка печатных и электронных изданий',
                'avatar' => 'assets/img/placeholder.jpg',
                'about_text' => 'Опытный верстальщик с многолетним стажем работы в области книжной верстки.',
                'primary_color' => '#2c3e50',
                'secondary_color' => '#3498db',
                'accent_color' => '#e74c3c',
                'text_color' => '#333333',
                'bg_color' => '#ffffff',
                'font_family' => 'Roboto',
                'bg_image' => '',
                'experience_years' => 5,
                'projects_count' => 100,
                'clients_count' => 50
            ];
            
            $insertSql = "INSERT INTO settings (site_title, hero_title, hero_subtitle, avatar, about_text, primary_color, secondary_color, accent_color, text_color, bg_color, font_family, bg_image, experience_years, projects_count, clients_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("ssssssssssssiii", 
                $defaultSettings['site_title'],
                $defaultSettings['hero_title'],
                $defaultSettings['hero_subtitle'],
                $defaultSettings['avatar'],
                $defaultSettings['about_text'],
                $defaultSettings['primary_color'],
                $defaultSettings['secondary_color'],
                $defaultSettings['accent_color'],
                $defaultSettings['text_color'],
                $defaultSettings['bg_color'],
                $defaultSettings['font_family'],
                $defaultSettings['bg_image'],
                $defaultSettings['experience_years'],
                $defaultSettings['projects_count'],
                $defaultSettings['clients_count']
            );
            $stmt->execute();
            $results[] = "✓ Добавлены настройки по умолчанию в таблицу settings";
            logMessage("Добавлены настройки по умолчанию");
        } else {
            $results[] = "- Таблица settings уже содержит данные";
        }
        
        // Шаг 4: Обновляем данные в таблице themes для правильного оформления
        logMessage("Обновление данных оформления в таблице themes");
        
        // Обновляем тему "Космос"
        $updateSpace = "UPDATE themes SET 
            primary_color = '#0f0c29',
            secondary_color = '#302b63', 
            accent_color = '#24243e',
            text_color = '#e0e0e0',
            bg_color = '#1a1a2e',
            font_family = 'Orbitron'
        WHERE name = 'Космос'";
        executeSQL($conn, $updateSpace);
        $results[] = "✓ Обновлена тема 'Космос'";
        
        // Обновляем тему "Вода"
        $updateWater = "UPDATE themes SET 
            primary_color = '#006ba6',
            secondary_color = '#0496ff', 
            accent_color = '#3da9fc',
            text_color = '#333333',
            bg_color = '#f0f8ff',
            font_family = 'Montserrat'
        WHERE name = 'Вода'";
        executeSQL($conn, $updateWater);
        $results[] = "✓ Обновлена тема 'Вода'";
        
        // Обновляем тему "Лес"
        $updateForest = "UPDATE themes SET 
            primary_color = '#1e3a1e',
            secondary_color = '#2d5a2d', 
            accent_color = '#4a7c59',
            text_color = '#333333',
            bg_color = '#f5f5dc',
            font_family = 'Roboto'
        WHERE name = 'Лес'";
        executeSQL($conn, $updateForest);
        $results[] = "✓ Обновлена тема 'Лес'";
        
        // Шаг 5: Проверяем финальную структуру
        logMessage("Проверка финальной структуры таблицы themes");
        $finalColumnsResult = $conn->query("SHOW COLUMNS FROM themes");
        $finalColumns = [];
        while ($row = $finalColumnsResult->fetch_assoc()) {
            $finalColumns[] = $row['Field'];
        }
        $results[] = "Финальные колонки в таблице themes: " . implode(', ', $finalColumns);
        
        // Шаг 6: Показываем текущие настройки
        $currentSettings = $conn->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();
        $results[] = "Текущие настройки сайта:";
        $results[] = "  - Название: " . $currentSettings['site_title'];
        $results[] = "  - Заголовок: " . $currentSettings['hero_title'];
        $results[] = "  - Подзаголовок: " . $currentSettings['hero_subtitle'];
        $results[] = "  - Лет опыта: " . $currentSettings['experience_years'];
        
        // Завершаем транзакцию
        $conn->commit();
        
        logMessage("Скрипт успешно завершен");
        $success = true;
        
    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $conn->rollback();
        $errors[] = "Ошибка: " . $e->getMessage();
        logMessage("Ошибка выполнения скрипта: " . $e->getMessage());
        $success = false;
    }
}

// Получаем текущую структуру таблиц для отображения
$themesColumns = [];
$settingsExists = false;

try {
    $columnsResult = $conn->query("SHOW COLUMNS FROM themes");
    while ($row = $columnsResult->fetch_assoc()) {
        $themesColumns[] = $row['Field'];
    }
    
    $settingsResult = $conn->query("SELECT * FROM settings LIMIT 1");
    $settingsExists = $settingsResult->num_rows > 0;
    $currentSettings = $settingsResult->fetch_assoc();
} catch (Exception $e) {
    $errors[] = "Ошибка при получении информации о таблицах: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Исправление структуры тем</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .result-item {
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
        .result-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .result-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .result-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .column-list {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">
                <i class="fas fa-tools"></i> Исправление структуры тем
            </h2>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Этот модуль предназначен для разделения личной информации и оформления. 
                Личная информация будет перенесена в таблицу settings, а таблица themes будет содержать только оформление.
            </div>
            
            <div class="warning-box">
                <h5><i class="fas fa-exclamation-triangle"></i> Внимание!</h5>
                <p>Этот скрипт внесет изменения в структуру базы данных. Рекомендуется сделать резервную копию перед выполнением.</p>
                <ul>
                    <li>Личная информация будет удалена из таблицы themes</li>
                    <li>Будут обновлены настройки оформления тем</li>
                    <li>Будут добавлены настройки по умолчанию, если они отсутствуют</li>
                </ul>
            </div>
            
            <!-- Текущее состояние -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Текущее состояние</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Колонки в таблице themes:</h6>
                            <div class="column-list">
                                <?php if (!empty($themesColumns)): ?>
                                    <?php foreach ($themesColumns as $column): ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?php safeEcho($column); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">Не удалось получить информацию</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Таблица settings:</h6>
                            <div class="column-list">
                                <?php if ($settingsExists): ?>
                                    <span class="badge bg-success me-1 mb-1">Данные существуют</span>
                                    <div class="mt-2">
                                        <small>
                                            <strong>Название:</strong> <?php safeEcho($currentSettings['site_title']); ?><br>
                                            <strong>Заголовок:</strong> <?php safeEcho($currentSettings['hero_title']); ?><br>
                                            <strong>Опыт:</strong> <?php safeEcho($currentSettings['experience_years']); ?> лет
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-warning me-1 mb-1">Таблица пуста</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Форма выполнения -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Выполнить исправление</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" onsubmit="return confirm('Вы уверены, что хотите выполнить исправление структуры?');">
                        <div class="mb-3">
                            <label class="form-label">
                                <input type="checkbox" name="confirm" required> 
                                Я понимаю, что будут внесены изменения в базу данных
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play"></i> Выполнить исправление
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Результаты выполнения -->
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Результаты выполнения</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="success-box">
                                <h5><i class="fas fa-check-circle"></i> Исправление успешно завершено!</h5>
                                <p>Структура базы данных была исправлена. Теперь личная информация хранится отдельно от оформления.</p>
                                <p><strong>Следующие шаги:</strong></p>
                                <ol>
                                    <li>Замените файл index.php на исправленную версию</li>
                                    <li>Обновите личную информацию в настройках сайта</li>
                                    <li>Проверьте результат на главной странице</li>
                                </ol>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h6>Ошибки:</h6>
                                <?php foreach ($errors as $error): ?>
                                    <div class="result-item result-error"><?php safeEcho($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($results)): ?>
                            <h6>Детали выполнения:</h6>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($results as $result): ?>
                                    <?php 
                                    $class = 'result-info';
                                    if (strpos($result, '✓') !== false) $class = 'result-success';
                                    if (strpos($result, 'Ошибка') !== false) $class = 'result-error';
                                    ?>
                                    <div class="result-item <?php echo $class; ?>"><?php safeEcho($result); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Инструкция -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Что делает этот скрипт?</h5>
                </div>
                <div class="card-body">
                    <h6>Шаг 1: Очистка таблицы themes</h6>
                    <p>Удаляет колонки с личной информацией из таблицы themes, оставляя только оформление:</p>
                    <ul>
                        <li>Удаляет: site_title, hero_title, hero_subtitle, avatar, about_text, experience_years, projects_count, clients_count</li>
                        <li>Оставляет: name, slug, description, primary_color, secondary_color, accent_color, text_color, bg_color, font_family, bg_image, is_active</li>
                    </ul>
                    
                    <h6>Шаг 2: Проверка таблицы settings</h6>
                    <p>Проверяет наличие данных в таблице settings и добавляет настройки по умолчанию, если таблица пуста.</p>
                    
                    <h6>Шаг 3: Обновление оформления тем</h6>
                    <p>Обновляет цвета и шрифты для стандартных тем (Космос, Вода, Лес) для правильного отображения.</p>
                    
                    <h6>Результат:</h6>
                    <p>После выполнения скрипта личная информация будет храниться в таблице settings, а оформление - в таблице themes. Это позволит независимо управлять контентом и дизайном сайта.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>