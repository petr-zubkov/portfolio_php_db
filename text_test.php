<?php
// Еще более простой тест - только PHP без HTML
header('Content-Type: text/plain; charset=utf-8');
echo "Простой тест PHP\n";
echo "Если вы видите этот текст, PHP работает.\n";
echo "Текущее время: " . date('Y-m-d H:i:s') . "\n";
echo "Версия PHP: " . phpversion() . "\n";
echo "Текущий файл: " . __FILE__ . "\n";
echo "Текущая директория: " . getcwd() . "\n";
?>