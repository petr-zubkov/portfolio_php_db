<?php
// Максимально простая главная страница
// Без зависимостей, без сложных функций, только базовый PHP и HTML
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мое портфолио</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar {
            background-color: #007bff;
            padding: 1rem;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .hero {
            background: linear-gradient(135deg, #007bff, #6c757d);
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
            background-color: #007bff;
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
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php">Мое портфолио</a>
            <a href="portfolio.php">Портфолио</a>
            <a href="profile.php">Профиль</a>
            <a href="contacts.php">Контакты</a>
        </div>
    </nav>

    <!-- Hero секция -->
    <section class="hero">
        <div class="container">
            <img src="assets/img/placeholder.jpg" alt="Аватар" class="avatar">
            <h1>Ваше имя</h1>
            <p>Ваша профессия</p>
            
            <!-- Космическая картинка -->
            <img src="https://images.unsplash.com/photo-1446776877081-d282a0f896e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Космос" class="space-image">
            
            <div>
                <a href="#about" class="btn btn-primary">Узнать больше</a>
                <a href="portfolio.php" class="btn btn-outline">Смотреть работы</a>
            </div>
        </div>
    </section>

    <!-- Обо мне -->
    <section id="about" class="section">
        <div class="container text-center">
            <h2>Обо мне</h2>
            <p>Расскажите о себе и своем опыте работы в этой области.</p>
            <p>Здесь можно разместить информацию о ваших навыках, достижениях и профессиональном пути.</p>
        </div>
    </section>

    <!-- Интересы -->
    <section class="section" style="background-color: #e9ecef;">
        <div class="container text-center">
            <h2>Мои интересы</h2>
            <p>Ваши хобби и увлечения</p>
            <p>Любимые фильмы, книги, музыка и другие интересы</p>
        </div>
    </section>

    <!-- Футер -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Мое портфолио. Все права защищены.</p>
            <p>Создано с помощью PHP и HTML/CSS</p>
        </div>
    </footer>

    <?php
    // Показываем информацию о PHP в конце страницы
    echo "<!-- PHP работает: " . date('Y-m-d H:i:s') . " -->";
    ?>
</body>
</html>