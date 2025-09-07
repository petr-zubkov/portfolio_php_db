<?php
session_start();
require_once 'config.php';

if (!isset($_GET['theme_id']) || !isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$theme_id = $_GET['theme_id'];
$theme_result = $conn->prepare("SELECT * FROM themes WHERE id = ?");
$theme_result->bind_param("i", $theme_id);
$theme_result->execute();
$theme = $theme_result->get_result()->fetch_assoc();

if (!$theme) {
    header('Location: index.php');
    exit;
}

// Получаем данные для отображения
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);

$skills_result = $conn->query("SELECT * FROM skills");
$skills = $skills_result->fetch_all(MYSQLI_ASSOC);

$contact_result = $conn->query("SELECT * FROM contact LIMIT 1");
$contact = $contact_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предпросмотр темы: <?php echo htmlspecialchars($theme['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($theme['font_family']); ?>&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/themes/<?php echo htmlspecialchars($theme['slug']); ?>.css" rel="stylesheet">
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
        
        /* Панель предпросмотра */
        .preview-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
            backdrop-filter: blur(10px);
            min-width: 250px;
        }
        
        .preview-panel h5 {
            margin: 0 0 15px 0;
            color: #fff;
        }
        
        .preview-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .preview-actions .btn {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Панель предпросмотра -->
        <div class="preview-panel">
            <h5><i class="fas fa-eye"></i> Предпросмотр темы</h5>
            <p><strong><?php echo htmlspecialchars($theme['name']); ?></strong></p>
            <p class="small"><?php echo htmlspecialchars($theme['description']); ?></p>
            <div class="preview-actions">
                <a href="admin/manage_themes.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
                <a href="admin/activate_theme.php?id=<?php echo $theme['id']; ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-check"></i> Активировать
                </a>
            </div>
        </div>

        <!-- Навигация -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#"><?php echo htmlspecialchars($theme['site_title'] ?? 'Портфолио'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#about">Обо мне</a></li>
                        <li class="nav-item"><a class="nav-link" href="#skills">Навыки</a></li>
                        <li class="nav-item"><a class="nav-link" href="#portfolio">Портфолио</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Контакты</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero секция -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <img src="<?php echo htmlspecialchars($theme['avatar'] ?? 'assets/img/placeholder.jpg'); ?>" alt="Аватар" class="hero-avatar mb-4">
                <h1 class="display-4 mb-3"><?php echo htmlspecialchars($theme['hero_title'] ?? 'Верстальщик'); ?></h1>
                <p class="lead mb-4"><?php echo htmlspecialchars($theme['hero_subtitle'] ?? 'Профессиональная верстка'); ?></p>
                <a href="#portfolio" class="btn btn-primary btn-lg">Смотреть работы</a>
            </div>
        </section>

        <!-- Обо мне -->
        <section id="about" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Обо мне</h2>
                <div class="row">
                    <div class="col-lg-6">
                        <p><?php echo nl2br(htmlspecialchars($theme['about_text'] ?? 'Опытный верстальщик с многолетним стажем работы.')); ?></p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-stats">
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($theme['experience_years'] ?? '5'); ?>+</h3>
                                <p>Лет опыта</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($theme['projects_count'] ?? '100'); ?>+</h3>
                                <p>Выполненных проектов</p>
                            </div>
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($theme['clients_count'] ?? '50'); ?>+</h3>
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

        <!-- Портфолио -->
        <section id="portfolio" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Портфолио</h2>
                <div class="row">
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="portfolio-card">
                                <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <div class="portfolio-overlay">
                                    <h5><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                                    <a href="<?php echo htmlspecialchars($project['link']); ?>" class="btn btn-primary">Подробнее</a>
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
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h5>Email</h5>
                                <p><?php echo htmlspecialchars($contact['email'] ?? 'your.email@example.com'); ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h5>Телефон</h5>
                                <p><?php echo htmlspecialchars($contact['phone'] ?? '+7 (999) 123-45-67'); ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-telegram"></i>
                            <div>
                                <h5>Telegram</h5>
                                <p><?php echo htmlspecialchars($contact['telegram'] ?? '@username'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-form">
                            <h5 class="mb-4">Отправить сообщение</h5>
                            <form method="POST" action="send_message.php">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ваше имя</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Сообщение</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Отправить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Футер -->
        <footer class="bg-dark text-white py-4">
            <div class="container text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($theme['site_title'] ?? 'Портфолио'); ?>. Все права защищены.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Плавная прокрутка для навигации
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Активация навигации при прокрутке
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').slice(1) === current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>