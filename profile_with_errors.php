<?php require_once 'header.php'; ?>

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

        <!-- Социальные сети -->
        <?php if (!empty($personal_info['social_links'])): ?>
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

<?php require_once 'footer.php'; ?>