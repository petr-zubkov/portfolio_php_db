<?php
// Рабочая версия главной страницы с правильной обработкой ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Запускаем сессию
session_start();

// Подключаем конфигурацию
require_once 'config.php';

// Значения по умолчанию
$personal_info = [
    'full_name' => 'Ваше имя',
    'profession' => 'Ваша профессия',
    'bio' => 'Расскажите о себе...',
    'avatar' => 'assets/img/placeholder.jpg',
    'experience_years' => 0,
    'projects_count' => 0,
    'clients_count' => 0,
    'hobbies' => [],
    'favorite_movies' => [],
    'my_books' => [],
    'websites' => []
];

$theme = [
    'site_title' => 'Портфолио',
    'primary_color' => '#007bff',
    'secondary_color' => '#6c757d',
    'accent_color' => '#28a745',
    'text_color' => '#333333',
    'bg_color' => '#ffffff',
    'font_family' => 'Arial',
    'bg_image' => '',
    'slug' => ''
];

$db_connected = false;
$db_error = '';

// Проверяем подключение к базе данных
if (isset($conn) && !$conn->connect_error) {
    $db_connected = true;
    
    // Получаем данные из БД
    $personal_info_result = $conn->query("SELECT * FROM personal_info LIMIT 1");
    if ($personal_info_result) {
        $personal_info_data = $personal_info_result->fetch_assoc();
        if ($personal_info_data) {
            $personal_info = array_merge($personal_info, $personal_info_data);
            
            // Декодируем JSON поля
            $personal_info['hobbies'] = json_decode($personal_info['hobbies'] ?: '[]', true);
            $personal_info['favorite_movies'] = json_decode($personal_info['favorite_movies'] ?: '[]', true);
            $personal_info['my_books'] = json_decode($personal_info['my_books'] ?: '[]', true);
            $personal_info['websites'] = json_decode($personal_info['websites'] ?: '[]', true);
        }
    }
    
    $theme_result = $conn->query("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
    if ($theme_result) {
        $theme_data = $theme_result->fetch_assoc();
        if ($theme_data) {
            $theme = array_merge($theme, $theme_data);
        }
    }
} else {
    $db_error = isset($conn) ? $conn->connect_error : 'Database connection not established';
}

// Функция hex2rgb() уже объявлена в config.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($theme['site_title']); ?></title>
    <style>
        body {
            font-family: <?php echo htmlspecialchars($theme['font_family']); ?>, sans-serif;
            margin: 0;
            padding: 0;
            background-color: <?php echo htmlspecialchars($theme['bg_color']); ?>;
            color: <?php echo htmlspecialchars($theme['text_color']); ?>;
        }
        .navbar {
            background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
            padding: 1rem;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .hero {
            background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
            color: white;
        }
        .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        .section {
            padding: 60px 0;
        }
        .text-center {
            text-align: center;
        }
        .space-image {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .hobby-item, .movie-item, .book-item {
            background: rgba(0,0,0,0.1);
            padding: 10px 15px;
            border-radius: 25px;
            margin: 5px;
            display: inline-block;
        }
        .website-item {
            background: rgba(0,0,0,0.1);
            padding: 10px 15px;
            border-radius: 25px;
            margin: 5px;
            display: inline-block;
        }
        .website-link {
            color: inherit;
            text-decoration: none;
        }
        .website-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php"><?php echo htmlspecialchars($theme['site_title']); ?></a>
            <a href="portfolio.php">Портфолио</a>
            <a href="profile.php">Профиль</a>
            <a href="contacts.php">Контакты</a>
        </div>
    </nav>

    <!-- Сообщение об ошибке базы данных -->
    <?php if (!$db_connected): ?>
    <div class="container">
        <div class="alert alert-warning">
            <h4>⚠ Внимание</h4>
            <p>Не удалось подключиться к базе данных: <?php echo htmlspecialchars($db_error); ?></p>
            <p>Сайт работает в ограниченном режиме. Пожалуйста, попробуйте обновить базу данных.</p>
            <a href="update_database.php" class="btn btn-primary">Обновить базу данных</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero секция -->
    <section class="hero">
        <div class="container">
            <img src="<?php echo htmlspecialchars($personal_info['avatar']); ?>" alt="Аватар" class="avatar">
            <h1><?php echo htmlspecialchars($personal_info['full_name']); ?></h1>
            <p><?php echo htmlspecialchars($personal_info['profession']); ?></p>
            
            <!-- Космическая картинка -->
            <img src="https://images.unsplash.com/photo-1446776877081-d282a0f896e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Космос" class="space-image">
            
            <div>
                <a href="#interests" class="btn btn-primary">Узнать больше</a>
                <a href="portfolio.php" class="btn btn-outline">Смотреть работы</a>
            </div>
        </div>
    </section>

    <!-- Личные интересы -->
    <section id="interests" class="section">
        <div class="container text-center">
            <h2>Личные интересы</h2>
            
            <!-- Хобби -->
            <?php if (!empty($personal_info['hobbies']) && is_array($personal_info['hobbies'])): ?>
            <div style="margin: 30px 0;">
                <h3>Мои хобби</h3>
                <?php foreach ($personal_info['hobbies'] as $hobby): ?>
                    <div class="hobby-item"><?php echo htmlspecialchars($hobby); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Любимые фильмы -->
            <?php if (!empty($personal_info['favorite_movies']) && is_array($personal_info['favorite_movies'])): ?>
            <div style="margin: 30px 0;">
                <h3>Любимые фильмы</h3>
                <?php foreach ($personal_info['favorite_movies'] as $movie): ?>
                    <div class="movie-item"><?php echo htmlspecialchars($movie); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Мои книги -->
            <?php if (!empty($personal_info['my_books']) && is_array($personal_info['my_books'])): ?>
            <div style="margin: 30px 0;">
                <h3>Мои книги</h3>
                <?php foreach ($personal_info['my_books'] as $book): ?>
                    <div class="book-item"><?php echo htmlspecialchars($book); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Сайты -->
            <?php if (!empty($personal_info['websites']) && is_array($personal_info['websites'])): ?>
            <div style="margin: 30px 0;">
                <h3>Мои сайты</h3>
                <?php foreach ($personal_info['websites'] as $website): ?>
                    <div class="website-item">
                        <a href="<?php echo htmlspecialchars($website['url']); ?>" target="_blank" class="website-link">
                            <?php echo htmlspecialchars($website['name']); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Обо мне -->
    <section class="section" style="background-color: #f8f9fa;">
        <div class="container text-center">
            <h2>Обо мне</h2>
            <p><?php echo nl2br(htmlspecialchars($personal_info['bio'])); ?></p>
            <div style="display: flex; justify-content: space-around; margin-top: 40px;">
                <div style="text-align: center;">
                    <h3><?php echo htmlspecialchars($personal_info['experience_years']); ?>+</h3>
                    <p>Лет опыта</p>
                </div>
                <div style="text-align: center;">
                    <h3><?php echo htmlspecialchars($personal_info['projects_count']); ?>+</h3>
                    <p>Выполненных проектов</p>
                </div>
                <div style="text-align: center;">
                    <h3><?php echo htmlspecialchars($personal_info['clients_count']); ?>+</h3>
                    <p>Довольных клиентов</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($theme['site_title']); ?>. Все права защищены.</p>
            <p>Создано с помощью PHP и HTML/CSS</p>
        </div>
    </footer>

</body>
</html>