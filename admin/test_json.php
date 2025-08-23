<?php
// Простой тест для проверки JSON ответа
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Тест JSON работает']);
?>