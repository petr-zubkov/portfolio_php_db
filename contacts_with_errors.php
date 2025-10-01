<?php require_once 'header.php'; ?>

        <!-- Hero секция для контактов -->
        <section id="hero" class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 mb-3">Контакты</h1>
                <p class="lead mb-4">Свяжитесь со мной любым удобным способом</p>
            </div>
        </section>

        <!-- Контакты -->
        <section id="contact" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Контактная информация</h2>
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
                        
                        <!-- Социальные сети из персональной информации -->
                        <?php if (!empty($personal_info['social_links'])): ?>
                            <?php foreach ($personal_info['social_links'] as $platform => $url): ?>
                                <?php if (!empty($url)): ?>
                                    <div class="contact-item">
                                        <i class="fab fa-<?php echo htmlspecialchars($platform); ?>"></i>
                                        <div>
                                            <h5><?php echo ucfirst(htmlspecialchars($platform)); ?></h5>
                                            <p><a href="<?php echo htmlspecialchars($url); ?>" target="_blank"><?php echo htmlspecialchars($url); ?></a></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Сайты из персональной информации -->
                        <?php if (!empty($personal_info['websites'])): ?>
                            <h4 class="mt-4 mb-3">Мои сайты</h4>
                            <?php foreach ($personal_info['websites'] as $website): ?>
                                <div class="contact-item">
                                    <i class="fas fa-globe"></i>
                                    <div>
                                        <h5><?php echo htmlspecialchars($website['name']); ?></h5>
                                        <p><a href="<?php echo htmlspecialchars($website['url']); ?>" target="_blank"><?php echo htmlspecialchars($website['url']); ?></a></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-form">
                            <h5 class="mb-4">Отправить сообщение</h5>
                            <form id="contactForm" method="POST" action="send_message.php">
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
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="btn-text">Отправить</span>
                                    <span class="btn-spinner d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Отправка...
                                    </span>
                                </button>
                            </form>
                            <div id="formMessage"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Карта (если нужно) -->
        <section id="map-section" class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Местоположение</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="location-card">
                            <div class="location-info">
                                <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                                <h4><?php echo htmlspecialchars($personal_info['location']); ?></h4>
                                <p>Доступен для удаленной работы и сотрудничества со всего мира</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Быстрые ссылки -->
        <section id="quick-links" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Быстрые ссылки</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="quick-links-grid">
                            <a href="index.php" class="quick-link-item">
                                <i class="fas fa-home"></i>
                                <span>Главная</span>
                            </a>
                            <a href="portfolio.php" class="quick-link-item">
                                <i class="fas fa-briefcase"></i>
                                <span>Портфолио</span>
                            </a>
                            <a href="profile.php" class="quick-link-item">
                                <i class="fas fa-user"></i>
                                <span>Профиль</span>
                            </a>
                            <a href="admin/" class="quick-link-item">
                                <i class="fas fa-cog"></i>
                                <span>Админ-панель</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<?php require_once 'footer.php'; ?>