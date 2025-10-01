<?php
session_start();
require_once 'config.php';

// Получаем данные из БД
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);

$skills_result = $conn->query("SELECT * FROM skills");
$skills = $skills_result->fetch_all(MYSQLI_ASSOC);

$contact_result = $conn->query("SELECT * FROM contact LIMIT 1");
$contact = $contact_result->fetch_assoc();

// Получаем персональную информацию
$personal_info_result = $conn->query("SELECT * FROM personal_info LIMIT 1");
$personal_info = $personal_info_result->fetch_assoc();

// Получаем активную тему
$theme_result = $conn->query("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
$theme = $theme_result->fetch_assoc();

// Если нет активной темы, используем настройки по умолчанию
if (!$theme) {
    $settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
    $settings = $settings_result->fetch_assoc();
    $theme = $settings;
}

// Если нет персональной информации, используем значения по умолчанию
if (!$personal_info) {
    $personal_info = [
        'full_name' => 'Ваше имя',
        'profession' => 'Ваша профессия',
        'bio' => 'Расскажите о себе...',
        'avatar' => 'assets/img/placeholder.jpg',
        'location' => 'Ваш город',
        'experience_years' => 0,
        'projects_count' => 0,
        'clients_count' => 0,
        'social_links' => '{}',
        'hobbies' => '[]',
        'favorite_movies' => '[]',
        'my_books' => '[]',
        'websites' => '[]'
    ];
} else {
    // Декодируем JSON поля с проверкой на существование
    $personal_info['social_links'] = json_decode($personal_info['social_links'] ?: '{}', true);
    $personal_info['hobbies'] = json_decode(isset($personal_info['hobbies']) ? $personal_info['hobbies'] : '[]', true);
    $personal_info['favorite_movies'] = json_decode(isset($personal_info['favorite_movies']) ? $personal_info['favorite_movies'] : '[]', true);
    $personal_info['my_books'] = json_decode(isset($personal_info['my_books']) ? $personal_info['my_books'] : '[]', true);
    $personal_info['websites'] = json_decode(isset($personal_info['websites']) ? $personal_info['websites'] : '[]', true);
}

// Функция для определения активной страницы
function getActivePage() {
    $current_file = basename($_SERVER['PHP_SELF']);
    $page_map = [
        'index.php' => 'home',
        'portfolio.php' => 'portfolio',
        'profile.php' => 'profile',
        'contacts.php' => 'contacts'
    ];
    return $page_map[$current_file] ?? 'home';
}

$active_page = getActivePage();

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
    $rgb = "$r, $g, $b";
    return $rgb;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($theme['site_title'] ?? 'Портфолио'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($theme['font_family']); ?>&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Динамическая загрузка CSS темы -->
    <?php if (!empty($theme['slug'])): ?>
    <link href="assets/css/themes/<?php echo htmlspecialchars($theme['slug']); ?>.css" rel="stylesheet">
    <?php endif; ?>
    
    <style>
        :root {
            --primary-color: <?php echo $theme['primary_color']; ?>;
            --secondary-color: <?php echo $theme['secondary_color']; ?>;
            --accent-color: <?php echo $theme['accent_color']; ?>;
            --text-color: <?php echo $theme['text_color']; ?>;
            --bg-color: <?php echo $theme['bg_color']; ?>;
            --font-family: '<?php echo $theme['font_family']; ?>', sans-serif;
        }
        <?php if (!empty($theme['bg_image'])): ?>
        body {
            background-image: url('<?php echo htmlspecialchars($theme['bg_image']); ?>');
            background-size: cover;
            background-attachment: fixed;
        }
        .content-wrapper {
            background-color: rgba(<?php echo hex2rgb($theme['bg_color']); ?>, 0.95);
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Навигация с выпадающим меню -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php"><?php echo htmlspecialchars($theme['site_title'] ?? 'Портфолио'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo $active_page === 'home' ? 'active' : ''; ?>" href="#" id="mainMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-home me-1"></i>Главная
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="mainMenuDropdown">
                                <li><a class="dropdown-item <?php echo $active_page === 'home' ? 'active' : ''; ?>" href="index.php"><i class="fas fa-home me-2"></i>Главная страница</a></li>
                                <li><a class="dropdown-item <?php echo $active_page === 'portfolio' ? 'active' : ''; ?>" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Портфолио</a></li>
                                <li><a class="dropdown-item <?php echo $active_page === 'profile' ? 'active' : ''; ?>" href="profile.php"><i class="fas fa-user me-2"></i>Профиль</a></li>
                                <li><a class="dropdown-item <?php echo $active_page === 'contacts' ? 'active' : ''; ?>" href="contacts.php"><i class="fas fa-envelope me-2"></i>Контакты</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_page === 'portfolio' ? 'active' : ''; ?>" href="portfolio.php">
                                <i class="fas fa-briefcase me-1"></i>Портфолио
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_page === 'profile' ? 'active' : ''; ?>" href="profile.php">
                                <i class="fas fa-user me-1"></i>Профиль
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_page === 'contacts' ? 'active' : ''; ?>" href="contacts.php">
                                <i class="fas fa-envelope me-1"></i>Контакты
                            </a>
                        </li>
                        <?php if (isset($_SESSION['admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/">
                                <i class="fas fa-cog me-1"></i>Админ-панель
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero секция для профиля -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <img src="<?php echo htmlspecialchars($personal_info['avatar']); ?>" alt="Аватар" class="hero-avatar mb-4">
                <h1 class="display-4 mb-3"><?php echo htmlspecialchars($personal_info['full_name']); ?></h1>
                <p class="lead mb-4"><?php echo htmlspecialchars($personal_info['profession']); ?></p>
                <div class="hero-location">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <?php echo htmlspecialchars($personal_info['location']); ?>
                </div>
            </div>
        </section>

        <!-- Обо мне -->
        <section id="about" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Обо мне</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="about-content">
                            <p><?php echo nl2br(htmlspecialchars($personal_info['bio'])); ?></p>
                        </div>
                        <div class="about-stats">
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['experience_years']); ?>+</h3>
                                <p>Лет опыта</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['projects_count']); ?>+</h3>
                                <p>Выполненных проектов</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['clients_count']); ?>+</h3>
                                <p>Довольных клиентов</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Навыки (полная версия) -->
        <section id="skills" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Мои навыки</h2>
                <div class="row">
                    <?php foreach ($skills as $skill): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="skill-card">
                                <i class="<?php echo htmlspecialchars($skill['icon']); ?> fa-3x mb-3"></i>
                                <h5><?php echo htmlspecialchars($skill['name']); ?></h5>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo htmlspecialchars($skill['level']); ?>%"></div>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($skill['level']); ?>%</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                        <h3 class="mb-4"><i class="fas fa-heart me-2"></i>Мои хобби</h3>
                        <div class="hobbies-grid">
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
                        <h3 class="mb-4"><i class="fas fa-film me-2"></i>Любимые фильмы</h3>
                        <div class="movies-grid">
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
                        <h3 class="mb-4"><i class="fas fa-book me-2"></i>Мои книги</h3>
                        <div class="books-grid">
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
                        <h3 class="mb-4"><i class="fas fa-globe me-2"></i>Мои сайты</h3>
                        <div class="websites-grid">
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

        <!-- Социальные сети -->
        <?php if (!empty($personal_info['social_links']) && is_array($personal_info['social_links'])): ?>
        <section id="social-links" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Социальные сети</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
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
            </div>
        </section>
        <?php endif; ?>

        <!-- Контакты (краткая версия) -->
        <section id="contact-preview" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Контакты</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="contact-preview-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="contact-mini-item">
                                        <i class="fas fa-envelope fa-2x mb-3"></i>
                                        <h5>Email</h5>
                                        <p><?php echo htmlspecialchars($contact['email'] ?? 'your.email@example.com'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="contact-mini-item">
                                        <i class="fas fa-phone fa-2x mb-3"></i>
                                        <h5>Телефон</h5>
                                        <p><?php echo htmlspecialchars($contact['phone'] ?? '+7 (999) 123-45-67'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="contact-mini-item">
                                        <i class="fab fa-telegram fa-2x mb-3"></i>
                                        <h5>Telegram</h5>
                                        <p><?php echo htmlspecialchars($contact['telegram'] ?? '@username'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="contacts.php" class="btn btn-primary btn-lg">Все контакты</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Футер -->
        <footer class="bg-dark text-white py-4">
            <div class="container text-center">
                <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($theme['site_title'] ?? 'Портфолио'); ?>. Все права защищены.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>