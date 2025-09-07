<?php
require_once 'config.php';

echo "<h2>Установка таблицы themes</h2>";

try {
    // Читаем SQL файл
    $sql = file_get_contents('create_themes_table.sql');
    
    // Выполняем SQL запросы
    if ($conn->multi_query($sql)) {
        echo "<div class='alert alert-success'>Таблица themes успешно создана и заполнена начальными данными!</div>";
        
        // Очищаем результаты
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
    } else {
        throw new Exception("Ошибка при выполнении SQL: " . $conn->error);
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Ошибка: " . $e->getMessage() . "</div>";
}

echo "<br><a href='admin/' class='btn btn-primary'>Перейти в админ-панель</a>";
?>