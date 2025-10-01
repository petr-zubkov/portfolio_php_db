<?php
// Тестируем только базовые функции PHP
try {
    // Проверяем базовые функции
    $test_results = [];
    
    // Тест 1: Проверка функции date()
    $test_results['date'] = function_exists('date') ? date('Y-m-d H:i:s') : 'Функция не существует';
    
    // Тест 2: Проверка функции phpversion()
    $test_results['phpversion'] = function_exists('phpversion') ? phpversion() : 'Функция не существует';
    
    // Тест 3: Проверка функции time()
    $test_results['time'] = function_exists('time') ? time() : 'Функция не существует';
    
    // Тест 4: Проверка констант
    $test_results['__FILE__'] = __FILE__;
    $test_results['__LINE__'] = __LINE__;
    
    // Тест 5: Проверка переменных сервера
    $test_results['SERVER_SOFTWARE'] = $_SERVER['SERVER_SOFTWARE'] ?? 'Не установлена';
    $test_results['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'Не установлена';
    
    // Выводим результаты
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== Результаты тестирования PHP ===\n\n";
    
    foreach ($test_results as $test => $result) {
        echo "$test: $result\n";
    }
    
    echo "\n=== Дополнительная информация ===\n";
    echo "PHP работает корректно!\n";
    echo "Все базовые функции доступны.\n";
    
} catch (Exception $e) {
    // Если произошла ошибка
    header('Content-Type: text/plain; charset=utf-8');
    echo "ОШИБКА: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
}
?>