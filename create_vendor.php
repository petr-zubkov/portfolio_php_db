<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Создание структуры для PHPMailer</h1>";

// Просто создаем базовую структуру
$dirs = [
    'vendor',
    'vendor/phpmailer',
    'vendor/phpmailer/phpmailer',
    'vendor/phpmailer/phpmailer/src'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div style='color: green;'>✅ Создана директория: $dir</div>";
        } else {
            echo "<div style='color: red;'>❌ Ошибка создания директории: $dir</div>";
        }
    } else {
        echo "<div style='color: blue;'>ℹ️ Директория уже существует: $dir</div>";
    }
}

// Создаем простой autoload.php
$autoload = '<?php
// Простой автозагрузчик
function phpmailer_autoload($class) {
    if (strpos($class, "PHPMailer\\\\") === 0) {
        $file = __DIR__ . "/phpmailer/phpmailer/src/" . str_replace("\\\\", "/", substr($class, 10)) . ".php";
        if (file_exists($file)) {
            require $file;
        }
    }
}
spl_autoload_register("phpmailer_autoload");
?>';

if (file_put_contents('vendor/autoload.php', $autoload)) {
    echo "<div style='color: green;'>✅ Создан autoload.php</div>";
} else {
    echo "<div style='color: red;'>❌ Ошибка создания autoload.php</div>";
}

echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
echo "<h2>✅ Готово!</h2>";
echo "<p>Базовая структура создана. Теперь можно использовать SMTP обработчик.</p>";
echo "</div>";

echo "<div style='margin: 20px 0;'>";
echo "<a href='QUICK_FIX.php' style='display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px;'>🚀 Быстрое исправление</a>";
echo "</div>";
?>