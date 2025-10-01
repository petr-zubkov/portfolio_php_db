<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест социальных сетей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/social-links-fix.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .social-demo {
            border: 2px dashed #dee2e6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <h1>🧪 Тест отображения социальных сетей</h1>
            <p>Эта страница демонстрирует, как теперь отображаются социальные сети - горизонтально с переносом на следующую строку.</p>
        </div>

        <div class="test-container">
            <h2>📱 Демонстрация с множеством социальных сетей</h2>
            <p>Здесь показано, как социальные сети располагаются горизонтально и переносятся на следующую строку, когда не помещаются:</p>
            
            <div class="social-demo">
                <h3>Пример 1: Много социальных сетей</h3>
                <div class="social-links-grid">
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-facebook fa-2x"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-twitter fa-2x"></i>
                            <span>Twitter</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-instagram fa-2x"></i>
                            <span>Instagram</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-linkedin fa-2x"></i>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-github fa-2x"></i>
                            <span>GitHub</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-youtube fa-2x"></i>
                            <span>YouTube</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-telegram fa-2x"></i>
                            <span>Telegram</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-vk fa-2x"></i>
                            <span>ВКонтакте</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-discord fa-2x"></i>
                            <span>Discord</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-tiktok fa-2x"></i>
                            <span>TikTok</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="social-demo">
                <h3>Пример 2: Несколько социальных сетей</h3>
                <div class="social-links-grid">
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-facebook fa-2x"></i>
                            <span>Facebook</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-instagram fa-2x"></i>
                            <span>Instagram</span>
                        </a>
                    </div>
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-telegram fa-2x"></i>
                            <span>Telegram</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="social-demo">
                <h3>Пример 3: Одна социальная сеть</h3>
                <div class="social-links-grid">
                    <div class="social-link-item">
                        <a href="#" target="_blank" class="social-link">
                            <i class="fab fa-github fa-2x"></i>
                            <span>GitHub</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="test-container">
            <h2>🔧 Что было изменено</h2>
            <ul>
                <li><strong>Было:</strong> Grid-сетка с большими ячейками (minmax(200px, 1fr))</li>
                <li><strong>Стало:</strong> Flexbox с горизонтальным расположением и переносом</li>
                <li><strong>Результат:</strong> Социальные сети теперь располагаются компактно горизонтально и переносятся на следующую строку</li>
            </ul>

            <h3>Ключевые изменения в CSS:</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
.social-links-grid {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    justify-content: center;
}

.social-link-item {
    min-width: 120px;
    flex: 0 1 auto;
}
            </pre>
        </div>

        <div class="test-container">
            <h2>📱 Адаптивность</h2>
            <p>Социальные сети адаптируются под разные размеры экрана:</p>
            <ul>
                <li><strong>Десктоп:</strong> Компактные карточки с отступами</li>
                <li><strong>Планшет (≤768px):</strong> Уменьшенные отступы и размеры</li>
                <li><strong>Мобильный (≤576px):</strong> Еще более компактное отображение</li>
            </ul>

            <p>Измените размер окна браузера, чтобы увидеть адаптивность в действии!</p>
        </div>

        <div class="test-container">
            <h2>🎯 Проверка на реальной странице</h2>
            <p>Теперь вы можете проверить отображение социальных сетей на странице профиля:</p>
            <a href="profile.php" class="btn btn-primary" target="_blank">Открыть страницу профиля</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>