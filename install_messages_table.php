<?php
require_once 'config.php';

// Устанавливаем заголовок
header('Content-Type: text/html; charset=utf-8');

try {
    // SQL-запрос для создания таблицы
    $sql = "CREATE TABLE IF NOT EXISTS `messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `message` text NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `status` enum('new', 'read', 'replied') NOT NULL DEFAULT 'new',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    // Выполняем запрос
    if ($conn->query($sql) === TRUE) {
        echo "<div style='color: green; font-family: Arial, sans-serif; padding: 20px;'>";
        echo "<h2>✅ Таблица 'messages' успешно создана!</h2>";
        echo "<p>Таблица для хранения сообщений от пользователей готова к использованию.</p>";
        echo "</div>";
    } else {
        throw new Exception("Ошибка при создании таблицы: " . $conn->error);
    }

    // Создаем индексы
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_messages_created_at ON messages(created_at)",
        "CREATE INDEX IF NOT EXISTS idx_messages_status ON messages(status)",
        "CREATE INDEX IF NOT EXISTS idx_messages_email ON messages(email)"
    ];

    foreach ($indexes as $index) {
        $conn->query($index);
    }

    echo "<div style='color: blue; font-family: Arial, sans-serif; padding: 20px;'>";
    echo "<h3>📊 Индексы успешно созданы</h3>";
    echo "<p>База данных оптимизирована для быстрой работы с сообщениями.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='color: red; font-family: Arial, sans-serif; padding: 20px;'>";
    echo "<h2>❌ Ошибка!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

$conn->close();
?>