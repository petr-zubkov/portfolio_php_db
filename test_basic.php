<?php
// Простая тестовая страница для проверки работы PHP без зависимости от базы данных
echo "<h1>Тестовая страница PHP (базовая)</h1>";
echo "<p>Если вы видите эту страницу, PHP работает корректно.</p>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Версия PHP: " . phpversion() . "</p>";

// Проверка базовых функций PHP
echo "<h2>Проверка базовых функций</h2>";
echo "<p>Функция date() работает: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Функция phpversion() работает: " . phpversion() . "</p>";

// Проверка расширений
echo "<h2>Проверка расширений PHP</h2>";
$extensions = ['mysqli', 'json', 'session', 'fileinfo'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>Расширение '$ext' установлено</p>";
    } else {
        echo "<p style='color: red;'>Расширение '$ext' не установлено</p>";
    }
}

// Проверка файлов
echo "<h2>Проверка файлов</h2>";
$files = ['config.php', 'index.php', 'update_database.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>Файл '$file' существует</p>";
    } else {
        echo "<p style='color: red;'>Файл '$file' не найден</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";
echo "<p><a href='update_database.php'>Обновить базу данных</a></p>";
echo "<p><a href='test.php'>Полный тест с базой данных</a></p>";
?>