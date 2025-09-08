<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Проверка установки модуля персональной информации</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-10'>
                <div class='card'>
                    <div class='card-header'>
                        <h2><i class='fas fa-check-circle'></i> Проверка установки модуля персональной информации</h2>
                    </div>
                    <div class='card-body'>
";

// Проверяем существование таблицы personal_info
$result = $conn->query("SHOW TABLES LIKE 'personal_info'");
if ($result->num_rows > 0) {
    echo "<div class='alert alert-success'>
        <i class='fas fa-check-circle'></i> <strong>Таблица personal_info существует</strong>
    </div>";
    
    // Проверяем наличие данных
    $personal_info = $conn->query("SELECT * FROM personal_info LIMIT 1")->fetch_assoc();
    if ($personal_info) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i> <strong>В таблице есть данные</strong>
        </div>";
        echo "<div class='card mb-3'>
            <div class='card-header'>
                <h5>Текущие данные в таблице personal_info</h5>
            </div>
            <div class='card-body'>
                <pre>" . htmlspecialchars(print_r($personal_info, true)) . "</pre>
            </div>
        </div>";
    } else {
        echo "<div class='alert alert-warning'>
            <i class='fas fa-exclamation-triangle'></i> <strong>Таблица существует, но данных нет</strong>
        </div>";
    }
} else {
    echo "<div class='alert alert-danger'>
        <i class='fas fa-times-circle'></i> <strong>Таблица personal_info не существует</strong>
    </div>";
    echo "<div class='alert alert-info'>
        <i class='fas fa-info-circle'></i> Для установки таблицы выполните:
        <a href='install_personal_info_table.php' class='btn btn-primary btn-sm mt-2'>
            <i class='fas fa-database'></i> Установить таблицу
        </a>
    </div>";
}

// Проверяем структуру таблиц themes и settings
echo "<div class='row mt-4'>
    <div class='col-md-6'>
        <div class='card'>
            <div class='card-header'>
                <h5><i class='fas fa-paint-brush'></i> Проверка таблицы themes</h5>
            </div>
            <div class='card-body'>";

$themes_columns = $conn->query("SHOW COLUMNS FROM themes");
$themes_fields = [];
while ($column = $themes_columns->fetch_assoc()) {
    $themes_fields[] = $column['Field'];
}

$personal_fields = ['hero_title', 'hero_subtitle', 'avatar', 'about_text', 'experience_years', 'projects_count', 'clients_count'];
$found_personal_fields = array_intersect($personal_fields, $themes_fields);

if (empty($found_personal_fields)) {
    echo "<div class='alert alert-success'>
        <i class='fas fa-check-circle'></i> В таблице themes нет персональных полей
    </div>";
} else {
    echo "<div class='alert alert-danger'>
        <i class='fas fa-times-circle'></i> В таблице themes найдены персональные поля: " . implode(', ', $found_personal_fields) . "
    </div>";
}

echo "            </div>
        </div>
    </div>
    
    <div class='col-md-6'>
        <div class='card'>
            <div class='card-header'>
                <h5><i class='fas fa-cog'></i> Проверка таблицы settings</h5>
            </div>
            <div class='card-body'>";

$settings_columns = $conn->query("SHOW COLUMNS FROM settings");
$settings_fields = [];
while ($column = $settings_columns->fetch_assoc()) {
    $settings_fields[] = $column['Field'];
}

$found_personal_fields = array_intersect($personal_fields, $settings_fields);

if (empty($found_personal_fields)) {
    echo "<div class='alert alert-success'>
        <i class='fas fa-check-circle'></i> В таблице settings нет персональных полей
    </div>";
} else {
    echo "<div class='alert alert-danger'>
        <i class='fas fa-times-circle'></i> В таблице settings найдены персональные поля: " . implode(', ', $found_personal_fields) . "
    </div>";
}

echo "            </div>
        </div>
    </div>
</div>";

// Проверяем файлы
echo "<div class='card mt-4'>
    <div class='card-header'>
        <h5><i class='fas fa-file-code'></i> Проверка файлов</h5>
    </div>
    <div class='card-body'>";

$required_files = [
    'admin/manage_personal_info.php',
    'create_personal_info_table.sql',
    'install_personal_info_table.php',
    'check_personal_info_install.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i> Файл <code>$file</code> существует
        </div>";
    } else {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-times-circle'></i> Файл <code>$file</code> не найден
        </div>";
    }
}

echo "        </div>
    </div>";

// Дальнейшие действия
echo "<div class='card mt-4'>
    <div class='card-header'>
        <h5><i class='fas fa-arrow-right'></i> Дальнейшие действия</h5>
    </div>
    <div class='card-body'>
        <div class='row'>
            <div class='col-md-4'>
                <a href='admin/manage_personal_info.php' class='btn btn-primary w-100 mb-2'>
                    <i class='fas fa-user-edit'></i><br>
                    Управление персональной информацией
                </a>
            </div>
            <div class='col-md-4'>
                <a href='admin/' class='btn btn-secondary w-100 mb-2'>
                    <i class='fas fa-tachometer-alt'></i><br>
                    Админ-панель
                </a>
            </div>
            <div class='col-md-4'>
                <a href='./' class='btn btn-info w-100 mb-2'>
                    <i class='fas fa-home'></i><br>
                    Главная страница сайта
                </a>
            </div>
        </div>
    </div>
</div>";

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