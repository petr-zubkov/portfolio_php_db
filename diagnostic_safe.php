<?php
// Комплексная страница для диагностики проблем (безопасная версия)
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Диагностика системы</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Диагностика системы</h1>

    <h2>Информация о PHP</h2>
    <p><strong>Версия PHP:</strong> <?php echo phpversion(); ?></p>
    <p><strong>Текущее время:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    <p><strong>Операционная система:</strong> <?php echo php_uname(); ?></p>
    <p><strong>Текущий скрипт:</strong> <?php echo __FILE__; ?></p>
    <p><strong>Текущая директория:</strong> <?php echo getcwd(); ?></p>

    <h2>Проверка расширений PHP</h2>
    <?php
    $required_extensions = ['mysqli', 'json', 'session', 'fileinfo', 'openssl'];
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<p class='success'>✓ Расширение '$ext' установлено</p>";
        } else {
            echo "<p class='error'>✗ Расширение '$ext' не установлено</p>";
        }
    }
    ?>

    <h2>Проверка конфигурационного файла</h2>
    <?php
    if (file_exists('config.php')) {
        echo "<p class='success'>✓ Файл config.php существует</p>";
        
        // Проверяем права доступа
        $perms = fileperms('config.php');
        echo "<p>Права доступа: " . substr(sprintf('%o', $perms), -4) . "</p>";
        
        // Пробуем включить файл с обработкой ошибок
        try {
            // Сначала проверим синтаксис файла
            $config_content = file_get_contents('config.php');
            if ($config_content === false) {
                echo "<p class='error'>✗ Не удалось прочитать файл config.php</p>";
            } else {
                echo "<p class='success'>✓ Файл config.php успешно прочитан</p>";
                
                // Попробуем включить файл, но с буферизацией вывода
                ob_start();
                $include_result = include_once 'config.php';
                $include_output = ob_get_clean();
                
                if ($include_output !== false) {
                    echo "<p class='success'>✓ Файл config.php успешно включен</p>";
                    
                    // Проверяем константы
                    $required_constants = ['DB_HOST', 'DB_USER', 'DB_NAME', 'UPLOAD_PATH'];
                    foreach ($required_constants as $const) {
                        if (defined($const)) {
                            echo "<p class='success'>✓ Константа '$const' определена</p>";
                        } else {
                            echo "<p class='error'>✗ Константа '$const' не определена</p>";
                        }
                    }
                } else {
                    echo "<p class='error'>✗ Ошибка при включении config.php</p>";
                    if (!empty($include_output)) {
                        echo "<pre>Вывод ошибки: " . htmlspecialchars($include_output) . "</pre>";
                    }
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Исключение при включении config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>✗ Файл config.php не найден</p>";
    }
    ?>

    <h2>Проверка подключения к базе данных</h2>
    <?php
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_NAME')) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                echo "<p class='error'>✗ Ошибка подключения: " . htmlspecialchars($conn->connect_error) . "</p>";
            } else {
                echo "<p class='success'>✓ Подключение к базе данных успешно</p>";
                
                // Проверка таблиц
                $tables = ['projects', 'skills', 'contact', 'personal_info', 'themes', 'settings'];
                foreach ($tables as $table) {
                    try {
                        $result = $conn->query("SHOW TABLES LIKE '$table'");
                        if ($result && $result->num_rows > 0) {
                            echo "<p class='success'>✓ Таблица '$table' существует</p>";
                        } else {
                            echo "<p class='warning'>⚠ Таблица '$table' не найдена</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p class='error'>✗ Ошибка при проверке таблицы '$table': " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
                
                $conn->close();
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Исключение при подключении: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='warning'>⚠ Пропуск проверки подключения - не все константы определены</p>";
    }
    ?>

    <h2>Проверка важных файлов</h2>
    <?php
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
            echo "<p class='success'>✓ Файл '$file' существует</p>";
        } else {
            echo "<p class='error'>✗ Файл '$file' не найден</p>";
        }
    }
    ?>

    <h2>Проверка директорий</h2>
    <?php
    $directories = [
        'uploads',
        'admin',
        'assets',
        'assets/css',
        'assets/js'
    ];

    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            echo "<p class='success'>✓ Директория '$dir' существует</p>";
            
            // Проверяем права на запись
            if (is_writable($dir)) {
                echo "<p class='success'>✓ Директория '$dir' доступна для записи</p>";
            } else {
                echo "<p class='warning'>⚠ Директория '$dir' не доступна для записи</p>";
            }
        } else {
            echo "<p class='error'>✗ Директория '$dir' не найдена</p>";
        }
    }
    ?>

    <h2>Проверка переменных сервера</h2>
    <?php
    $server_vars = ['SERVER_SOFTWARE', 'REQUEST_METHOD', 'SCRIPT_NAME', 'PHP_SELF', 'DOCUMENT_ROOT'];
    foreach ($server_vars as $var) {
        if (isset($_SERVER[$var])) {
            echo "<p><strong>$var:</strong> " . htmlspecialchars($_SERVER[$var]) . "</p>";
        } else {
            echo "<p class='warning'><strong>$var:</strong> не установлена</p>";
        }
    }
    ?>

    <h2>Проверка функций PHP</h2>
    <?php
    $functions = ['date', 'phpversion', 'file_exists', 'is_dir', 'extension_loaded'];
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "<p class='success'>✓ Функция '$func' доступна</p>";
        } else {
            echo "<p class='error'>✗ Функция '$func' недоступна</p>";
        }
    }
    ?>

    <hr>
    <h2>Ссылки для тестирования</h2>
    <ul>
        <li><a href="simple_test.php">Простой тест PHP</a></li>
        <li><a href="test_basic.php">Базовый тест PHP</a></li>
        <li><a href="test_db_connection.php">Тест подключения к базе данных</a></li>
        <li><a href="test.php">Обновленный тест PHP</a></li>
        <li><a href="index_safe.php">Безопасная версия главной страницы</a></li>
        <li><a href="update_database.php">Обновление базы данных</a></li>
    </ul>
</body>
</html>