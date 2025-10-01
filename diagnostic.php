<?php
// Комплексная страница для диагностики проблем
echo "<h1>Диагностика системы</h1>";

// 1. Базовая информация о PHP
echo "<h2>Информация о PHP</h2>";
echo "<p>Версия PHP: " . phpversion() . "</p>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Операционная система: " . php_uname() . "</p>";
echo "<p>Текущий скрипт: " . __FILE__ . "</p>";
echo "<p>Текущая директория: " . getcwd() . "</p>";

// 2. Проверка расширений
echo "<h2>Проверка расширений PHP</h2>";
$required_extensions = ['mysqli', 'json', 'session', 'fileinfo', 'openssl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ Расширение '$ext' установлено</p>";
    } else {
        echo "<p style='color: red;'>✗ Расширение '$ext' не установлено</p>";
    }
}

// 3. Проверка конфигурационного файла
echo "<h2>Проверка конфигурационного файла</h2>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ Файл config.php существует</p>";
    
    // Проверяем права доступа
    $perms = fileperms('config.php');
    echo "<p>Права доступа: " . substr(sprintf('%o', $perms), -4) . "</p>";
    
    // Пробуем включить файл
    try {
        include_once 'config.php';
        echo "<p style='color: green;'>✓ Файл config.php успешно включен</p>";
        
        // Проверяем константы
        $required_constants = ['DB_HOST', 'DB_USER', 'DB_NAME', 'UPLOAD_PATH'];
        foreach ($required_constants as $const) {
            if (defined($const)) {
                echo "<p style='color: green;'>✓ Константа '$const' определена</p>";
            } else {
                echo "<p style='color: red;'>✗ Константа '$const' не определена</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Ошибка при включении config.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Файл config.php не найден</p>";
}

// 4. Проверка подключения к базе данных
echo "<h2>Проверка подключения к базе данных</h2>";
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_NAME')) {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            echo "<p style='color: red;'>✗ Ошибка подключения: " . $conn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Подключение к базе данных успешно</p>";
            
            // Проверка таблиц
            $tables = ['projects', 'skills', 'contact', 'personal_info', 'themes', 'settings'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    echo "<p style='color: green;'>✓ Таблица '$table' существует</p>";
                } else {
                    echo "<p style='color: orange;'>⚠ Таблица '$table' не найдена</p>";
                }
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Исключение при подключении: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Пропуск проверки подключения - не все константы определены</p>";
}

// 5. Проверка важных файлов
echo "<h2>Проверка важных файлов</h2>";
$important_files = [
    'index.php',
    'portfolio.php', 
    'profile.php',
    'contacts.php',
    'update_database.php',
    'header.php',
    'footer.php'
];

foreach ($important_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ Файл '$file' существует</p>";
    } else {
        echo "<p style='color: red;'>✗ Файл '$file' не найден</p>";
    }
}

// 6. Проверка директорий
echo "<h2>Проверка директорий</h2>";
$directories = [
    'uploads',
    'admin',
    'assets',
    'assets/css',
    'assets/js'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<p style='color: green;'>✓ Директория '$dir' существует</p>";
        
        // Проверяем права на запись
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✓ Директория '$dir' доступна для записи</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Директория '$dir' не доступна для записи</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Директория '$dir' не найдена</p>";
    }
}

echo "<hr>";
echo "<h2>Ссылки для тестирования</h2>";
echo "<ul>";
echo "<li><a href='test_basic.php'>Базовый тест PHP</a></li>";
echo "<li><a href='test_db_connection.php'>Тест подключения к базе данных</a></li>";
echo "<li><a href='index.php'>Главная страница</a></li>";
echo "<li><a href='update_database.php'>Обновление базы данных</a></li>";
echo "</ul>";
?>