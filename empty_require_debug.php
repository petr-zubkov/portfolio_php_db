<?php
// Пустой файл с require_once() и отладкой
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting require test...<br>";

try {
    require_once 'config.php';
    echo "Config file included successfully!<br>";
    echo "Time: " . date('Y-m-d H:i:s') . "<br>";
    
    if (isset($conn)) {
        echo "Database connection variable is set<br>";
        if ($conn->connect_error) {
            echo "Database connection error: " . htmlspecialchars($conn->connect_error) . "<br>";
        } else {
            echo "Database connection successful!<br>";
        }
    } else {
        echo "Database connection variable is not set<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Test completed.";
?>