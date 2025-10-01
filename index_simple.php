<?php
// Максимально простая версия главной страницы без зависимостей
session_start();
header('Content-Type: text/html; charset=utf-8');

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

// Пробуем подключиться к базе данных, но не прерываем выполнение при ошибке
$db_connected = false;
$db_error = '';
try {
    if (file_exists('config.php')) {
        // Читаем config.php как текст, чтобы проверить синтаксис
        $config_content = file_get_contents('config.php');
        if ($config_content !== false) {
            // Пробуем включить с буферизацией вывода
            ob_start();
            $include_result = @include_once 'config.php';
            $include_output = ob_get_clean();
            
            if ($include_result !== false && isset($conn) && !$conn->connect_error) {
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
                $db_error = 'Ошибка подключения к базе данных';
            }
        } else {
            $db_error = 'Не удалось прочитать config.php';
        }
    } else {
        $db_error = 'Файл config.php не найден';
    }
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

// Функция для преобразования HEX в RGB
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "$r, $g, $b";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($theme['site_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: <?php echo htmlspecialchars($theme['font_family']); ?>, sans-serif;
            background-color: <?php echo htmlspecialchars($theme['bg_color']); ?>;
            color: <?php echo htmlspecialchars($theme['text_color']); ?>;
        }
        .hero-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid <?php echo htmlspecialchars($theme['primary_color']); ?>;
        }
        .btn-primary {
            background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
            border-color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
        }
        .btn-outline-light {
            color: #fff;
            border-color: #fff;
        }
        .btn-outline-light:hover {
            background-color: #fff;
            color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
        }
        .hero-section {
            background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>);
            color: white;
            padding: 100px 0;
            margin-top: 56px;
        }
        .space-image {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .hobby-item, .movie-item, .book-item {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 25px;
            margin: 5px;
            display: inline-block;
        }
        .website-item {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 25px;
            margin: 5px;
            display: inline-block;
        }
        .website-link {
            color: white;
            text-decoration: none;
        }
        .website-link:hover {
            color: #fff;
            text-decoration: underline;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        .stat-item h3 {
            font-size: 2.5rem;
            font-weight: bold;
            color: <?php echo htmlspecialchars($theme['primary_color']); ?>;
        }
        .navbar {
            background-color: <?php echo htmlspecialchars($theme['primary_color']); ?> !important;
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo htmlspecialchars($theme['site_title']); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Главная
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">
                            <i class="fas fa-briefcase me-1"></i>Портфолио
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>Профиль
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacts.php">
                            <i class="fas fa-envelope me-1"></i>Контакты
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Сообщение об ошибке базы данных -->
    <?php if (!$db_connected): ?>
    <div class="container mt-5">
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Внимание</h4>
            <p>Не удалось подключиться к базе данных: <?php echo htmlspecialchars($db_error); ?></p>
            <p>Сайт работает в ограниченном режиме. Пожалуйста, попробуйте обновить базу данных.</p>
            <a href="update_database.php" class="btn btn-warning">Обновить базу данных</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero секция -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <img src="<?php echo htmlspecialchars($personal_info['avatar']); ?>" alt="Аватар" class="hero-avatar mb-4">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($personal_info['full_name']); ?></h1>
                    <p class="lead mb-4"><?php echo htmlspecialchars($personal_info['profession']); ?></p>
                    
                    <!-- Космическая картинка -->
                    <div class="mb-4">
                        <img src="https://images.unsplash.com/photo-1446776877081-d282a0f896e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Космос" class="space-image">
                    </div>
                    
                    <div class="hero-buttons">
                        <a href="#personal-interests" class="btn btn-primary btn-lg me-3">Узнать больше</a>
                        <a href="portfolio.php" class="btn btn-outline-light btn-lg">Смотреть работы</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Личные интересы -->
    <section id="personal-interests" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Личные интересы</h2>
            
            <!-- Хобби -->
            <?php if (!empty($personal_info['hobbies']) && is_array($personal_info['hobbies'])): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4 text-center"><i class="fas fa-heart me-2"></i>Мои хобби</h3>
                    <div class="text-center">
                        <?php foreach ($personal_info['hobbies'] as $hobby): ?>
                            <div class="hobby-item">
                                <i class="fas fa-star me-2"></i><?php echo htmlspecialchars($hobby); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Любимые фильмы -->
            <?php if (!empty($personal_info['favorite_movies']) && is_array($personal_info['favorite_movies'])): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4 text-center"><i class="fas fa-film me-2"></i>Любимые фильмы</h3>
                    <div class="text-center">
                        <?php foreach ($personal_info['favorite_movies'] as $movie): ?>
                            <div class="movie-item">
                                <i class="fas fa-video me-2"></i><?php echo htmlspecialchars($movie); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Мои книги -->
            <?php if (!empty($personal_info['my_books']) && is_array($personal_info['my_books'])): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h3 class="mb-4 text-center"><i class="fas fa-book me-2"></i>Мои книги</h3>
                    <div class="text-center">
                        <?php foreach ($personal_info['my_books'] as $book): ?>
                            <div class="book-item">
                                <i class="fas fa-book-open me-2"></i><?php echo htmlspecialchars($book); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Сайты -->
            <?php if (!empty($personal_info['websites']) && is_array($personal_info['websites'])): ?>
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4 text-center"><i class="fas fa-globe me-2"></i>Мои сайты</h3>
                    <div class="text-center">
                        <?php foreach ($personal_info['websites'] as $website): ?>
                            <div class="website-item">
                                <a href="<?php echo htmlspecialchars($website['url']); ?>" target="_blank" class="website-link">
                                    <i class="fas fa-external-link-alt me-2"></i><?php echo htmlspecialchars($website['name']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Обо мне -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Обо мне</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="about-content text-center">
                        <p><?php echo nl2br(htmlspecialchars($personal_info['bio'])); ?></p>
                    </div>
                    <div class="about-stats">
                        <div class="row">
                            <div class="col-md-4 stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['experience_years']); ?>+</h3>
                                <p>Лет опыта</p>
                            </div>
                            <div class="col-md-4 stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['projects_count']); ?>+</h3>
                                <p>Выполненных проектов</p>
                            </div>
                            <div class="col-md-4 stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['clients_count']); ?>+</h3>
                                <p>Довольных клиентов</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($theme['site_title']); ?>. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Создано с <i class="fas fa-heart text-danger"></i> с помощью PHP и Bootstrap</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>