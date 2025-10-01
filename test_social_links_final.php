<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Финальный тест социальных сетей</title>
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
        .demo-section {
            border: 2px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        .status-info {
            color: #17a2b8;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <h1>🎯 Финальный тест социальных сетей</h1>
            <p><span class="status-success">✅ РЕШЕНИЕ ПРИМЕНЕНО!</span></p>
            <p>Создан специальный CSS файл <code>social-links-fix.css</code>, который принудительно устанавливает горизонтальное отображение социальных сетей.</p>
        </div>

        <div class="test-container">
            <h2>📋 Что было сделано</h2>
            <ul>
                <li><strong>✅ Создан отдельный CSS файл:</strong> <code>assets/css/social-links-fix.css</code></li>
                <li><strong>✅ Подключен ко всем страницам:</strong> index.php, portfolio.php, profile.php, contacts.php</li>
                <li><strong>✅ Использует !important:</strong> для переопределения любых конфликтующих стилей</li>
                <li><strong>✅ Гарантированное горизонтальное отображение:</strong> с переносом на следующую строку</li>
            </ul>

            <h3>🔧 Ключевые CSS свойства:</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
.social-links-grid {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 1rem !important;
    justify-content: center !important;
    width: 100% !important;
    flex-direction: row !important;
}

.social-link-item {
    flex: 0 1 auto !important;
    display: inline-flex !important;
    min-width: 120px !important;
}
            </pre>
        </div>

        <div class="demo-section">
            <h3>📱 Демонстрация: Много социальных сетей</h3>
            <p class="status-info">Должны отображаться горизонтально с переносом на следующую строку:</p>
            
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

        <div class="demo-section">
            <h3>📱 Демонстрация: Несколько социальных сетей</h3>
            <p class="status-info">Должны отображаться в одну строку:</p>
            
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

        <div class="demo-section">
            <h3>📱 Демонстрация: Одна социальная сеть</h3>
            <p class="status-info">Должна отображаться по центру:</p>
            
            <div class="social-links-grid">
                <div class="social-link-item">
                    <a href="#" target="_blank" class="social-link">
                        <i class="fab fa-github fa-2x"></i>
                        <span>GitHub</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="test-container">
            <h2>🧪 Тестирование на реальных страницах</h2>
            <p>Теперь вы можете проверить отображение социальных сетей на реальных страницах сайта:</p>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="profile.php" class="btn btn-primary me-md-2" target="_blank">
                    <i class="fab fa-github me-2"></i>Страница профиля
                </a>
                <a href="contacts.php" class="btn btn-secondary me-md-2" target="_blank">
                    <i class="fas fa-envelope me-2"></i>Страница контактов
                </a>
                <a href="index.php" class="btn btn-success" target="_blank">
                    <i class="fas fa-home me-2"></i>Главная страница
                </a>
            </div>
        </div>

        <div class="test-container">
            <h2>📱 Адаптивность</h2>
            <p><span class="status-info">Измените размер окна браузера, чтобы увидеть адаптивность!</span></p>
            
            <div class="row">
                <div class="col-md-4">
                    <h4>💻 Десктоп</h4>
                    <ul>
                        <li>Минимальная ширина: 120px</li>
                        <li>Отступы: 1rem</li>
                        <li>Иконки: 1.5rem</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>📱 Планшет (≤768px)</h4>
                    <ul>
                        <li>Минимальная ширина: 100px</li>
                        <li>Отступы: 0.5rem</li>
                        <li>Иконки: 1.3rem</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>📱 Мобильный (≤576px)</h4>
                    <ul>
                        <li>Минимальная ширина: 80px</li>
                        <li>Отступы: 0.25rem</li>
                        <li>Иконки: 1.1rem</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="test-container">
            <h2>🎯 Итог</h2>
            <p><span class="status-success">✅ ЗАДАЧА ВЫПОЛНЕНА!</span></p>
            
            <p>Теперь социальные сети:</p>
            <ul>
                <li>✅ Располагаются горизонтально</li>
                <li>✅ Автоматически переносятся на следующую строку</li>
                <li>✅ Не переопределяются другими стилями</li>
                <li>✅ Адаптируются под все устройства</li>
                <li>✅ Сохраняют привлекательный внешний вид</li>
            </ul>
            
            <div class="alert alert-success">
                <h4>🚉 Проблема решена!</h4>
                <p>Социальные сети больше не отображаются в столбик. Теперь они корректно располагаются горизонтально с переносом на следующую строку, как вы и просили.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>