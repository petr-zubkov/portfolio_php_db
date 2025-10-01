<?php
require_once 'config.php';

echo "<h1>Добавление тестовых данных социальных сетей</h1>";

// Проверяем, есть ли данные в personal_info
$result = $conn->query("SELECT * FROM personal_info LIMIT 1");
$personal_info = $result->fetch_assoc();

if ($personal_info) {
    echo "<h2>Текущие данные:</h2>";
    echo "<pre>";
    print_r($personal_info);
    echo "</pre>";
    
    // Декодируем текущие социальные ссылки
    $current_social = json_decode($personal_info['social_links'] ?: '{}', true);
    echo "<h3>Текущие социальные ссылки:</h3>";
    echo "<pre>";
    print_r($current_social);
    echo "</pre>";
    
    // Добавляем тестовые данные
    $test_social_links = [
        'facebook' => 'https://facebook.com/testuser',
        'twitter' => 'https://twitter.com/testuser',
        'instagram' => 'https://instagram.com/testuser',
        'linkedin' => 'https://linkedin.com/in/testuser',
        'github' => 'https://github.com/testuser',
        'youtube' => 'https://youtube.com/testuser',
        'telegram' => 'https://t.me/testuser',
        'vk' => 'https://vk.com/testuser',
        'tenchat' => 'https://tenchat.ru/testuser',
        'ok' => 'https://ok.ru/testuser'
    ];
    
    // Обновляем данные
    $social_links_json = json_encode($test_social_links);
    
    $update_query = "UPDATE personal_info SET social_links = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $social_links_json, $personal_info['id']);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>";
        echo "<h3>✅ Данные успешно обновлены!</h3>";
        echo "<p>Социальные ссылки добавлены в базу данных.</p>";
        echo "</div>";
        
        // Проверяем обновленные данные
        $result = $conn->query("SELECT * FROM personal_info LIMIT 1");
        $updated_info = $result->fetch_assoc();
        $updated_social = json_decode($updated_info['social_links'], true);
        
        echo "<h3>Обновленные социальные ссылки:</h3>";
        echo "<pre>";
        print_r($updated_social);
        echo "</pre>";
        
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<h3>❌ Ошибка при обновлении данных:</h3>";
        echo "<p>" . $conn->error . "</p>";
        echo "</div>";
    }
    
} else {
    echo "<div class='alert alert-warning'>";
    echo "<h3>⚠️ Данные в personal_info не найдены</h3>";
    echo "<p>Сначала нужно добавить персональную информацию.</p>";
    echo "</div>";
    
    // Создаем тестовую запись
    $test_personal_info = [
        'full_name' => 'Тестовый пользователь',
        'profession' => 'Веб-разработчик',
        'bio' => 'Тестовая биография для проверки социальных сетей',
        'avatar' => 'assets/img/placeholder.jpg',
        'location' => 'Москва',
        'experience_years' => 5,
        'projects_count' => 20,
        'clients_count' => 15,
        'social_links' => json_encode([
            'facebook' => 'https://facebook.com/testuser',
            'twitter' => 'https://twitter.com/testuser',
            'instagram' => 'https://instagram.com/testuser',
            'linkedin' => 'https://linkedin.com/in/testuser',
            'github' => 'https://github.com/testuser',
            'youtube' => 'https://youtube.com/testuser',
            'telegram' => 'https://t.me/testuser',
            'vk' => 'https://vk.com/testuser',
            'tenchat' => 'https://tenchat.ru/testuser',
            'ok' => 'https://ok.ru/testuser'
        ]),
        'hobbies' => json_encode(['Программирование', 'Музыка', 'Спорт']),
        'favorite_movies' => json_encode(['Матрица', 'Интерстеллар']),
        'my_books' => json_encode(['Код да Винчи', '1984']),
        'websites' => json_encode([])
    ];
    
    $columns = implode(', ', array_keys($test_personal_info));
    $placeholders = implode(', ', array_fill(0, count($test_personal_info), '?'));
    $types = str_repeat('s', count($test_personal_info));
    
    $insert_query = "INSERT INTO personal_info ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($insert_query);
    
    $values = array_values($test_personal_info);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>";
        echo "<h3>✅ Тестовые данные успешно добавлены!</h3>";
        echo "<p>Создана запись в personal_info с социальными ссылками.</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<h3>❌ Ошибка при добавлении данных:</h3>";
        echo "<p>" . $conn->error . "</p>";
        echo "</div>";
    }
}

echo "<div class='mt-4'>";
echo "<h3>🔗 Ссылки для проверки:</h3>";
echo "<ul>";
echo "<li><a href='profile.php' target='_blank'>Страница профиля</a></li>";
echo "<li><a href='test_social_icons_working.php' target='_blank'>Тестовая страница</a></li>";
echo "<li><a href='test_social_links_clean.php' target='_blank'>Чистый тест</a></li>";
echo "</ul>";
echo "</div>";

echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".alert { padding: 15px; margin: 10px 0; border-radius: 5px; }";
echo ".alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }";
echo ".alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }";
echo ".alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }";
echo "pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }";
echo "</style>";
?>