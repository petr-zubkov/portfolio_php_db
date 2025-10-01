<?php
// Тест сессий
session_start();
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Тест сессий</title>";
echo "<meta charset='utf-8'>";
echo "</head>";
echo "<body>";
echo "<h1>Тест сессий PHP</h1>";
echo "<p>Если вы видите эту страницу, session_start() работает.</p>";
echo "<p>Текущее время: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>ID сессии: " . session_id() . "</p>";
echo "<p>Статус сессии: " . session_status() . "</p>";
echo "</body>";
echo "</html>";
?>