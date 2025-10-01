<?php
// Самый простой тест session_start()
session_start();
echo "Session test started successfully!";
echo "<br>Session ID: " . session_id();
echo "<br>Time: " . date('Y-m-d H:i:s');
?>