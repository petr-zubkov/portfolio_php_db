<?php
// Тестирование всех основных страниц сайта
echo "<h1>Тестирование всех страниц сайта</h1>";

$pages_to_test = [
    'index.php' => 'Главная страница',
    'portfolio.php' => 'Портфолио',
    'profile.php' => 'Профиль',
    'contacts.php' => 'Контакты'
];

echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<tr><th>Страница</th><th>Статус</th><th>Результат</th></tr>";

foreach ($pages_to_test as $page => $description) {
    echo "<tr>";
    echo "<td><strong>$description</strong><br><small>$page</small></td>";
    
    // Проверяем существование файла
    if (file_exists($page)) {
        // Пытаемся включить файл для проверки на синтаксические ошибки
        try {
            // Используем output buffering для перехвата любого вывода
            ob_start();
            
            // Создаем заглушку для функций, которые могут вызывать ошибки при прямом включении
            if (!function_exists('session_start')) {
                function session_start() { return true; }
            }
            
            // Проверяем синтаксис с помощью php_check_syntax (если доступно) или через include
            $syntax_check = true;
            if (function_exists('php_check_syntax')) {
                $syntax_check = php_check_syntax($page);
            } else {
                // Альтернативная проверка - пытаемся включить файл в изолированной среде
                $included = @include_once $page;
                if ($included === false && error_get_last()) {
                    $syntax_check = false;
                }
            }
            
            ob_end_clean();
            
            if ($syntax_check) {
                echo "<td style='color: green;'>✅ OK</td>";
                echo "<td>Файл существует и синтаксически корректен</td>";
            } else {
                echo "<td style='color: red;'>❌ Ошибка</td>";
                echo "<td>Синтаксическая ошибка в файле</td>";
            }
        } catch (Exception $e) {
            echo "<td style='color: red;'>❌ Ошибка</td>";
            echo "<td>Исключение: " . htmlspecialchars($e->getMessage()) . "</td>";
        }
    } else {
        echo "<td style='color: red;'>❌ Ошибка</td>";
        echo "<td>Файл не существует</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

echo "<h2>Дополнительная проверка</h2>";

// Проверяем наличие функции hex2rgb в config.php
if (file_exists('config.php')) {
    $config_content = file_get_contents('config.php');
    if (strpos($config_content, 'function hex2rgb') !== false) {
        echo "<p style='color: green;'>✅ Функция hex2rgb найдена в config.php</p>";
    } else {
        echo "<p style='color: red;'>❌ Функция hex2rgb НЕ найдена в config.php</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Файл config.php не найден</p>";
}

// Проверяем, что функция не объявлена в основных файлах
foreach ($pages_to_test as $page => $description) {
    if (file_exists($page)) {
        $content = file_get_contents($page);
        if (strpos($content, 'function hex2rgb') !== false) {
            echo "<p style='color: red;'>❌ Повторное объявление hex2rgb найдено в $page</p>";
        } else {
            echo "<p style='color: green;'>✅ Дубликата hex2rgb нет в $page</p>";
        }
    }
}

echo "<h2>Рекомендации</h2>";
echo "<ul>";
echo "<li>Все основные страницы должны быть доступны без ошибок 500</li>";
echo "<li>Функция hex2rgb должна быть объявлена только в config.php</li>";
echo "<li>Проверьте работу меню и кнопок на сайте</li>";
echo "</ul>";

echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";
?>