<?php
// Пустой файл с session_start() и отладкой
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting session test...<br>";

try {
    session_start();
    echo "Session started successfully!<br>";
    echo "Session ID: " . session_id() . "<br>";
    echo "Time: " . date('Y-m-d H:i:s') . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Test completed.";
?>