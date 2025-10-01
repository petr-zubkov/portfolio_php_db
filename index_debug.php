<?php
// Промежуточная версия главной страницы с отладкой
session_start();
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Промежуточная тестовая страница</title>";
echo "<meta charset='utf-8'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }";
echo ".navbar { background-color: #007bff; padding: 1rem; margin: -20px -20px 20px -20px; color: white; }";
echo ".navbar a { color: white; text-decoration: none; margin: 0 15px; }";
echo ".hero { background: linear-gradient(135deg, #007bff, #6c757d); color: white; padding: 60px 20px; text-align: center; margin: 20px -20px; }";
echo ".avatar { width: 150px; height: 150px; border-radius: 50%; border: 4px solid white; margin-bottom: 20px; }";
echo ".btn { display: inline-block; padding: 10px 20px; margin: 10px; text-decoration: none; border-radius: 5px; }";
echo ".btn-primary { background-color: #007bff; color: white; }";
echo ".btn-outline { background-color: transparent; color: white; border: 2px solid white; }";
echo ".debug { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 20px 0; border-radius: 5px; }";
echo ".success { color: green; }";
echo ".error { color: red; }";
echo ".warning { color: orange; }";
echo "</style>";
echo "</head>";
echo "<body>";

// Отладочная информация
echo "<div class='debug'>";
echo "<h3>Отладочная информация</h3>";
echo "<p><strong>Время:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>ID сессии:</strong> " . session_id() . "</p>";
echo "<p><strong>Текущий файл:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Текущая директория:</strong> " . getcwd() . "</p>";
echo "</div>";

// Навигация
echo "<nav class='navbar'>";
echo "<a href='index.php'>Мое портфолио</a>";
echo "<a href='portfolio.php'>Портфолио</a>";
echo "<a href='profile.php'>Профиль</a>";
echo "<a href='contacts.php'>Контакты</a>";
echo "</nav>";

// Hero секция
echo "<section class='hero'>";
echo "<img src='assets/img/placeholder.jpg' alt='Аватар' class='avatar'>";
echo "<h1>Ваше имя</h1>";
echo "<p>Ваша профессия</p>";
echo "<img src='https://images.unsplash.com/photo-1446776877081-d282a0f896e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80' alt='Космос' style='max-width: 100%; height: auto; border-radius: 15px; margin: 20px 0;'>";
echo "<div>";
echo "<a href='#about' class='btn btn-primary'>Узнать больше</a>";
echo "<a href='portfolio.php' class='btn btn-outline'>Смотреть работы</a>";
echo "</div>";
echo "</section>";

// Тест подключения config.php
echo "<div class='debug'>";
echo "<h3>Тест подключения config.php</h3>";
if (file_exists('config.php')) {
    echo "<p class='success'>✓ Файл config.php существует</p>";
    
    ob_start();
    try {
        $include_result = include_once 'config.php';
        $include_output = ob_get_clean();
        
        if ($include_result !== false) {
            echo "<p class='success'>✓ Файл config.php успешно включен</p>";
            
            if (isset($conn)) {
                echo "<p class='success'>✓ Переменная \$conn установлена</p>";
                if ($conn->connect_error) {
                    echo "<p class='warning'>⚠ Ошибка подключения к базе данных: " . htmlspecialchars($conn->connect_error) . "</p>";
                } else {
                    echo "<p class='success'>✓ Подключение к базе данных успешно</p>";
                }
            } else {
                echo "<p class='error'>✗ Переменная \$conn не установлена</p>";
            }
            
        } else {
            echo "<p class='error'>✗ Ошибка при включении config.php</p>";
        }
        
        if (!empty($include_output)) {
            echo "<p><strong>Вывод при включении:</strong> " . htmlspecialchars($include_output) . "</p>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p class='error'>✗ Исключение при включении config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p class='error'>✗ Файл config.php не найден</p>";
}
echo "</div>";

// Обо мне
echo "<section id='about'>";
echo "<h2>Обо мне</h2>";
echo "<p>Расскажите о себе и своем опыте работы в этой области.</p>";
echo "<p>Здесь можно разместить информацию о ваших навыках, достижениях и профессиональном пути.</p>";
echo "</section>";

// Футер
echo "<footer style='background-color: #343a40; color: white; padding: 20px; text-align: center; margin: 20px -20px -20px -20px;'>";
echo "<p>&copy; " . date('Y') . " Мое портфолио. Все права защищены.</p>";
echo "<p>Создано с помощью PHP и HTML/CSS</p>";
echo "</footer>";

echo "</body>";
echo "</html>";
?>