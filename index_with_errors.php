<?php require_once 'header.php'; ?>

        <!-- Hero секция с космической темой -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <img src="<?php echo htmlspecialchars($personal_info['avatar']); ?>" alt="Аватар" class="hero-avatar mb-4">
                        <h1 class="display-4 mb-3"><?php echo htmlspecialchars($personal_info['full_name']); ?></h1>
                        <p class="lead mb-4"><?php echo htmlspecialchars($personal_info['profession']); ?></p>
                        
                        <!-- Красивая космическая картинка (не на всю страницу) -->
                        <div class="space-image-container mb-4">
                            <img src="https://images.unsplash.com/photo-1446776877081-d282a0f896e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Космос" class="space-image img-fluid rounded shadow-lg">
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
                <?php if (!empty($personal_info['hobbies'])): ?>
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
                <?php if (!empty($personal_info['favorite_movies'])): ?>
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
                <?php if (!empty($personal_info['my_books'])): ?>
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
                <?php if (!empty($personal_info['websites'])): ?>
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

        <!-- Краткая информация обо мне -->
        <section id="about" class="py-5 bg-light">
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

        <!-- Навыки (краткая версия) -->
        <section id="skills" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Ключевые навыки</h2>
                <div class="row">
                    <?php 
                    // Показываем только первые 6 навыков на главной странице
                    $main_skills = array_slice($skills, 0, 6);
                    foreach ($main_skills as $skill): 
                    ?>
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
                <div class="text-center mt-4">
                    <a href="profile.php" class="btn btn-primary">Все навыки</a>
                </div>
            </div>
        </section>

        <!-- Последние проекты -->
        <section id="recent-projects" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Последние проекты</h2>
                <div class="row">
                    <?php 
                    // Показываем только последние 3 проекта на главной странице
                    $recent_projects = array_slice($projects, 0, 3);
                    foreach ($recent_projects as $project): 
                    ?>
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
                <div class="text-center mt-4">
                    <a href="portfolio.php" class="btn btn-primary">Все проекты</a>
                </div>
            </div>
        </section>

<?php require_once 'footer.php'; ?>