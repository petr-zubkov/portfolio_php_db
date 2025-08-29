<?php
// Отключаем вывод ошибок
ini_set('display_errors', 0);
error_reporting(0);

// Буферизация вывода
ob_start();

session_start();
if (!isset($_SESSION['admin'])) {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require_once '../config.php';

// Функция логирования
function logMessage($message) {
    $logFile = __DIR__ . '/logs/sql.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Устанавливаем заголовок
header('Content-Type: application/json');

try {
    logMessage("=== Начало обработки execute_sql.php ===");
    
    // Проверяем метод
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        logMessage("Метод не POST: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Only POST method allowed');
    }
    
    logMessage("Метод: POST");
    
    // Получаем SQL запрос
    $sql_query = isset($_POST['sql_query']) ? trim($_POST['sql_query']) : '';
    
    if (empty($sql_query)) {
        logMessage("SQL запрос пустой");
        throw new Exception('SQL query is required');
    }
    
    logMessage("SQL запрос: " . substr($sql_query, 0, 200) . "...");
    
    // Проверяем подключение
    if (!$conn || $conn->connect_error) {
        logMessage("Ошибка подключения: " . ($conn->connect_error ?? 'Unknown'));
        throw new Exception('Database connection failed');
    }
    
    logMessage("Подключение к БД OK");
    
    // Определяем тип запроса
    $sql_upper = strtoupper(trim($sql_query));
    $is_select = strpos($sql_upper, 'SELECT') === 0;
    $is_insert = strpos($sql_upper, 'INSERT') === 0;
    $is_update = strpos($sql_upper, 'UPDATE') === 0;
    $is_delete = strpos($sql_upper, 'DELETE') === 0;
    
    // Защита от опасных операций
    $dangerous_keywords = ['DROP', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];
    foreach ($dangerous_keywords as $keyword) {
        if (strpos($sql_upper, $keyword) !== false) {
            logMessage("Обнаружена опасная операция: $keyword");
            throw new Exception("Операция $keyword запрещена для безопасности");
        }
    }
    
    // Начинаем транзакцию для модифицирующих запросов
    if ($is_insert || $is_update || $is_delete) {
        $conn->begin_transaction();
        logMessage("Транзакция начата");
    }
    
    try {
        if ($is_select) {
            // Выполняем SELECT запрос
            $result = $conn->query($sql_query);
            
            if (!$result) {
                logMessage("Ошибка выполнения SELECT: " . $conn->error);
                throw new Exception('Query failed: ' . $conn->error);
            }
            
            // Получаем результаты
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            $result->free();
            
            $response = [
                'success' => true,
                'message' => 'Запрос успешно выполнен',
                'query_type' => 'SELECT',
                'rows_affected' => count($data),
                'data' => $data
            ];
            
            logMessage("SELECT выполнен, получено строк: " . count($data));
            
        } elseif ($is_insert || $is_update || $is_delete) {
            // Выполняем модифицирующий запрос
            $result = $conn->query($sql_query);
            
            if (!$result) {
                logMessage("Ошибка выполнения запроса: " . $conn->error);
                throw new Exception('Query failed: ' . $conn->error);
            }
            
            $affected_rows = $conn->affected_rows;
            $insert_id = $conn->insert_id;
            
            // Завершаем транзакцию
            $conn->commit();
            logMessage("Транзакция завершена");
            
            $response = [
                'success' => true,
                'message' => 'Запрос успешно выполнен',
                'query_type' => strtoupper(substr($sql_upper, 0, 6)),
                'rows_affected' => $affected_rows,
                'insert_id' => $insert_id
            ];
            
            logMessage("Запрос выполнен, затронуто строк: $affected_rows");
            
        } else {
            // Другие типы запросов (SHOW, DESCRIBE и т.д.)
            $result = $conn->query($sql_query);
            
            if (!$result) {
                logMessage("Ошибка выполнения запроса: " . $conn->error);
                throw new Exception('Query failed: ' . $conn->error);
            }
            
            // Если есть результат (как у SELECT)
            if ($result instanceof mysqli_result) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
                
                $response = [
                    'success' => true,
                    'message' => 'Запрос успешно выполнен',
                    'query_type' => 'OTHER',
                    'rows_affected' => count($data),
                    'data' => $data
                ];
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Запрос успешно выполнен',
                    'query_type' => 'OTHER',
                    'rows_affected' => $conn->affected_rows
                ];
            }
            
            logMessage("Запрос выполнен успешно");
        }
        
        // Очищаем буфер и выводим результат
        ob_end_clean();
        echo json_encode($response);
        
    } catch (Exception $e) {
        // Откатываем транзакцию если была начата
        if ($is_insert || $is_update || $is_delete) {
            $conn->rollback();
            logMessage("Транзакция откачена: " . $e->getMessage());
        }
        throw $e;
    }
    
} catch (Exception $e) {
    logMessage("Фатальная ошибка: " . $e->getMessage());
    
    // Очищаем буфер
    ob_end_clean();
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    logMessage("Ошибка ответа: " . json_encode($response));
    echo json_encode($response);
}

// Закрываем соединение
if (isset($conn)) {
    $conn->close();
}

logMessage("=== Завершение обработки ===");
exit;
?>