<?php
session_start();
require_once 'config.php';

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
    // Декодируем JSON поля
    $personal_info['social_links'] = json_decode($personal_info['social_links'] ?: '{}', true);
    $personal_info['hobbies'] = json_decode($personal_info['hobbies'] ?: '[]', true);
    $personal_info['favorite_movies'] = json_decode($personal_info['favorite_movies'] ?: '[]', true);
    $personal_info['my_books'] = json_decode($personal_info['my_books'] ?: '[]', true);
    $personal_info['websites'] = json_decode($personal_info['websites'] ?: '[]', true);
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