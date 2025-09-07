<?php
session_start();
require_once 'config.php';

echo "<h2>Установка портфолио</h2>";

try {
    // Читаем и выполняем SQL файлы
    $sql_files = ['database.sql', 'create_themes_table.sql'];
    
    foreach ($sql_files as $file) {
        if (file_exists($file)) {
            echo "<h3>Установка файла: $file</h3>";
            $sql = file_get_contents($file);
            
            if ($conn->multi_query($sql)) {
                echo "<div class='alert alert-success'>Файл $file успешно установлен!</div>";
                
                // Очищаем результаты
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
                
            } else {
                throw new Exception("Ошибка при выполнении SQL из файла $file: " . $conn->error);
            }
        } else {
            echo "<div class='alert alert-warning'>Файл $file не найден</div>";
        }
    }
    
    // Создаем изображение-заглушку, если его нет
    if (!file_exists('assets/img/placeholder.jpg')) {
        include 'create_placeholder.php';
        echo "<div class='alert alert-info'>Изображение-заглушка создано</div>";
    }
    
    echo "<div class='alert alert-success'>";
    echo "<h4>Установка завершена успешно!</h4>";
    echo "<p>Теперь вы можете:</p>";
    echo "<ul>";
    echo "<li><a href='admin/' class='btn btn-primary'>Перейти в админ-панель</a></li>";
    echo "<li><a href='index.php' class='btn btn-success'>Посмотреть сайт</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Ошибка: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка портфолио</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2><i class="fas fa-download"></i> Установка портфолио</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        // Код установки будет выполнен выше
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>