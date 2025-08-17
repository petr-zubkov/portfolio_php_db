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

$settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($settings['font_family']); ?>&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?php echo $settings['primary_color']; ?>;
            --secondary-color: <?php echo $settings['secondary_color']; ?>;
            --accent-color: <?php echo $settings['accent_color']; ?>;
            --text-color: <?php echo $settings['text_color']; ?>;
            --bg-color: <?php echo $settings['bg_color']; ?>;
            --font-family: '<?php echo $settings['font_family']; ?>', sans-serif;
        }
        <?php if (!empty($settings['bg_image'])): ?>
        body {
            background-image: url('<?php echo htmlspecialchars($settings['bg_image']); ?>');
            background-size: cover;
            background-attachment: fixed;
        }
        .content-wrapper {
            background-color: rgba(<?php echo hex2rgb($settings['bg_color']); ?>, 0.95);
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Навигация -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#"><?php echo htmlspecialchars($settings['site_title']); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#about">Обо мне</a></li>
                        <li class="nav-item"><a class="nav-link" href="#skills">Навыки</a></li>
                        <li class="nav-item"><a class="nav-link" href="#portfolio">Портфолио</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
                        <?php if (isset($_SESSION['admin'])): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/">Админ-панель</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero секция -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <img src="<?php echo htmlspecialchars($settings['avatar']); ?>" alt="Аватар" class="hero-avatar mb-4">
                <h1 class="display-4 mb-3"><?php echo htmlspecialchars($settings['hero_title']); ?></h1>
                <p class="lead mb-4"><?php echo htmlspecialchars($settings['hero_subtitle']); ?></p>
                <a href="#portfolio" class="btn btn-primary btn-lg">Смотреть работы</a>
            </div>
        </section>

        <!-- Обо мне -->
        <section id="about" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Обо мне</h2>
                <div class="row">
                    <div class="col-lg-6">
                        <p><?php echo nl2br(htmlspecialchars($settings['about_text'])); ?></p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-stats">
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($settings['experience_years']); ?>+</h3>
                                <p>Лет опыта</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($settings['projects_count']); ?>+</h3>
                                <p>Выполненных проектов</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($settings['clients_count']); ?>+</h3>
                                <p>Довольных клиентов</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Навыки -->
        <section id="skills" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Навыки</h2>
                <div class="row">
                    <?php foreach ($skills as $skill): ?>
                    <div class="col-md-4 mb-4">
                        <div class="skill-card">
                            <i class="<?php echo htmlspecialchars($skill['icon']); ?> fa-3x mb-3"></i>
                            <h4><?php echo htmlspecialchars($skill['name']); ?></h4>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo (int)$skill['level']; ?>%">
                                    <?php echo (int)$skill['level']; ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Портфолио -->
        <section id="portfolio" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Портфолио</h2>
                <div class="row">
                    <?php foreach ($projects as $project): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="portfolio-card">
                            <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                            <div class="portfolio-overlay">
                                <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                                <p><?php echo htmlspecialchars($project['description']); ?></p>
                                <a href="<?php echo htmlspecialchars($project['link']); ?>" class="btn btn-outline-light" target="_blank">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Контакты -->
        <section id="contact" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Контакты</h2>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-envelope fa-2x"></i>
                                <div>
                                    <h4>Email</h4>
                                    <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone fa-2x"></i>
                                <div>
                                    <h4>Телефон</h4>
                                    <a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"><?php echo htmlspecialchars($contact['phone']); ?></a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fab fa-telegram fa-2x"></i>
                                <div>
                                    <h4>Telegram</h4>
                                    <a href="https://t.me/<?php echo htmlspecialchars($contact['telegram']); ?>" target="_blank">@<?php echo htmlspecialchars($contact['telegram']); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form id="contactForm" class="contact-form">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Ваше имя" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Ваш email" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Ваше сообщение" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Отправить сообщение</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Футер -->
        <footer class="bg-dark text-white py-4">
            <div class="container text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['site_title']); ?>. Все права защищены.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r,$g,$b";
}
?>