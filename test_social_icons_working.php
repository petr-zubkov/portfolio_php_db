<?php
session_start();
require_once 'config.php';

// Получаем персональную информацию
$personal_info_result = $conn->query("SELECT * FROM personal_info LIMIT 1");
$personal_info = $personal_info_result->fetch_assoc();

// Если нет персональной информации, используем тестовые данные
if (!$personal_info) {
    $personal_info = [
        'full_name' => 'Тестовый пользователь',
        'profession' => 'Веб-разработчик',
        'bio' => 'Тестовая биография',
        'avatar' => 'assets/img/placeholder.jpg',
        'location' => 'Москва',
        'experience_years' => 5,
        'projects_count' => 20,
        'clients_count' => 15,
        'social_links' => '{"facebook":"https://facebook.com","twitter":"https://twitter.com","instagram":"https://instagram.com","linkedin":"https://linkedin.com","youtube":"https://youtube.com","tenchat":"https://tenchat.ru","ok":"https://ok.ru"}',
        'hobbies' => '["Программирование", "Музыка", "Спорт"]',
        'favorite_movies' => '["Матрица", "Интерстеллар"]',
        'my_books' => '["Код да Винчи", "1984"]',
        'websites' => '[]'
    ];
} else {
    // Декодируем JSON поля
    $personal_info['social_links'] = json_decode($personal_info['social_links'] ?: '{}', true);
    $personal_info['hobbies'] = json_decode(isset($personal_info['hobbies']) ? $personal_info['hobbies'] : '[]', true);
    $personal_info['favorite_movies'] = json_decode(isset($personal_info['favorite_movies']) ? $personal_info['favorite_movies'] : '[]', true);
    $personal_info['my_books'] = json_decode(isset($personal_info['my_books']) ? $personal_info['my_books'] : '[]', true);
    $personal_info['websites'] = json_decode(isset($personal_info['websites']) ? $personal_info['websites'] : '[]', true);
}

// Получаем активную тему
$theme_result = $conn->query("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
$theme = $theme_result->fetch_assoc();

if (!$theme) {
    $settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
    $settings = $settings_result->fetch_assoc();
    $theme = $settings;
}

// Функция hex2rgb() уже объявлена в config.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест социальных ссылок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($theme['font_family'] ?? 'Roboto'); ?>&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/social-links-fix.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: <?php echo $theme['primary_color'] ?? '#3498db'; ?>;
            --secondary-color: <?php echo $theme['secondary_color'] ?? '#2c3e50'; ?>;
            --accent-color: <?php echo $theme['accent_color'] ?? '#e74c3c'; ?>;
            --text-color: <?php echo $theme['text_color'] ?? '#333333'; ?>;
            --bg-color: <?php echo $theme['bg_color'] ?? '#ffffff'; ?>;
            --font-family: '<?php echo $theme['font_family'] ?? 'Roboto'; ?>', sans-serif;
        }
        
        body {
            padding: 40px 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-family);
        }
        
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-section">
            <h1 class="text-center mb-4">Тест социальных ссылок</h1>
            <p class="text-center text-muted">Проверка отображения иконок и горизонтального расположения</p>
            
            <!-- Социальные сети -->
            <?php if (!empty($personal_info['social_links']) && is_array($personal_info['social_links'])): ?>
            <div class="row">
                <div class="col-12">
                    <h3 class="text-center mb-4">Социальные сети</h3>
                    <div class="social-links-grid">
                        <?php foreach ($personal_info['social_links'] as $platform => $url): ?>
                            <?php if (!empty($url)): ?>
                                <div class="social-link-item">
                                    <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="social-link">
                                        <i class="fab fa-<?php echo htmlspecialchars($platform); ?> fa-2x"></i>
                                        <span><?php echo ucfirst(htmlspecialchars($platform)); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                Социальные сети не найдены в базе данных
            </div>
            <?php endif; ?>
            
            <!-- Отладочная информация -->
            <div class="debug-info">
                <h4>Отладочная информация:</h4>
                <p><strong>Социальные ссылки из БД:</strong></p>
                <pre><?php print_r($personal_info['social_links']); ?></pre>
                <p><strong>Текущий CSS:</strong></p>
                <p>Файл: assets/css/social-links-fix.css</p>
                <p><strong>Font Awesome:</strong> Подключен версии 6.4.0</p>
            </div>
        </div>
        
        <div class="text-center">
            <a href="profile.php" class="btn btn-primary">Перейти на страницу профиля</a>
            <a href="index.php" class="btn btn-secondary">Перейти на главную</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>