<?php
// Максимально простой тест - только базовый HTML и PHP
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Простой тест</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Простой тест PHP</h1>";
echo "<p>Если вы видите эту страницу, PHP работает.</p>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Версия PHP: " . phpversion() . "</p>";
echo "</body>";
echo "</html>";
?>