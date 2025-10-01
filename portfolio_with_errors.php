<?php require_once 'header.php'; ?>

        <!-- Hero секция для портфолио -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 mb-3">Моё портфолио</h1>
                <p class="lead mb-4">Профессиональные проекты и работы</p>
                <div class="portfolio-stats">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-item">
                                <h3><?php echo count($projects); ?></h3>
                                <p>Всего проектов</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['experience_years']); ?>+</h3>
                                <p>Лет опыта</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <h3><?php echo htmlspecialchars($personal_info['clients_count']); ?>+</h3>
                                <p>Довольных клиентов</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Портфолио -->
        <section id="portfolio" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Все проекты</h2>
                
                <?php if (!empty($projects)): ?>
                    <div class="row">
                        <?php foreach ($projects as $project): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="portfolio-card">
                                    <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                    <div class="portfolio-overlay">
                                        <h5><?php echo htmlspecialchars($project['title']); ?></h5>
                                        <p><?php echo htmlspecialchars($project['description']); ?></p>
                                        <div class="project-meta">
                                            <small class="text-light">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d.m.Y', strtotime($project['created_at'])); ?>
                                            </small>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($project['link']); ?>" class="btn btn-primary mt-3">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Проекты пока не добавлены. Скоро здесь появится моя работа!
                        </div>
                    </div>
                <?php endif; ?>
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

        <!-- Контакты (краткая версия) -->
        <section id="contact-preview" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Свяжитесь со мной</h2>
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