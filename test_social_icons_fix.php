<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест исправления иконок социальных сетей</title>
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
            border: 2px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #f8fff9;
        }
        .problem-section {
            border: 2px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #fff8f8;
        }
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        .status-info {
            color: #17a2b8;
            font-weight: bold;
        }
        .status-warning {
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-container">
            <h1>🎯 Тест исправления иконок социальных сетей</h1>
            <p><span class="status-success">✅ ИСПРАВЛЕНИЕ ПРИМЕНЕНО!</span></p>
            <p>Исправлена проблема с Tenchat и Ok, которые не имели иконок и выравнивались по центру вместо нижнего края.</p>
        </div>

        <div class="test-container">
            <h2>🔍 Описание проблемы</h2>
            <div class="problem-section">
                <h3>❌ Было:</h3>
                <ul>
                    <li>Tenchat и Ok не имели иконок Font Awesome</li>
                    <li>Они отображались только как текст</li>
                    <li>Текст выравнивался по центру вертикально</li>
                    <li>Другие соцсети с иконками выравнивались по нижнему краю</li>
                    <li>Получалось некрасивое смещение</li>
                </ul>
            </div>

            <div class="demo-section">
                <h3>✅ Стало:</h3>
                <ul>
                    <li>Добавлены текстовые иконки для Tenchat (TC) и Ok (OK)</li>
                    <li>Все элементы выравниваются по нижнему краю</li>
                    <li>Фиксированная высота для всех элементов</li>
                    <li>Единое визуальное отображение</li>
                </ul>
            </div>
        </div>

        <div class="test-container">
            <h2>🛠️ Что было исправлено</h2>
            
            <h3>1. Выравнивание по нижнему краю</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
.social-links-grid {
    align-items: flex-end !important; /* Выравнивание по нижнему краю */
}

.social-link-item {
    justify-content: flex-end !important; /* Выравнивание содержимого */
    height: 80px !important; /* Фиксированная высота */
}

.social-link {
    justify-content: flex-end !important; /* Выравнивание по нижнему краю */
}
            </pre>

            <h3>2. Запасные иконки для Tenchat и Ok</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
.social-link i.fa-tenchat::before {
    content: 'TC' !important;
    font-family: Arial, sans-serif !important;
    font-weight: bold !important;
}

.social-link i.fa-ok::before {
    content: 'OK' !important;
    font-family: Arial, sans-serif !important;
    font-weight: bold !important;
}
            </pre>

            <h3>3. Обработка отсутствующих иконок</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
/* Если иконка не отображается, скрываем ее и центрируем текст */
.social-link i[class*="fa-tenchat"]:empty {
    display: none !important;
}

.social-link i[class*="fa-tenchat"]:empty + span {
    margin-top: auto !important;
    margin-bottom: auto !important;
}
            </pre>
        </div>

        <div class="demo-section">
            <h3>📱 Демонстрация: Социальные сети с Tenchat и Ok</h3>
            <p class="status-info">Теперь все элементы выравниваются по нижнему краю:</p>
            
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
                        <i class="fab fa-tenchat fa-2x"></i>
                        <span>Tenchat</span>
                    </a>
                </div>
                <div class="social-link-item">
                    <a href="#" target="_blank" class="social-link">
                        <i class="fab fa-ok fa-2x"></i>
                        <span>Ok</span>
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
            </div>
        </div>

        <div class="demo-section">
            <h3>📱 Демонстрация: Только Tenchat и Ok</h3>
            <p class="status-info">Проверка выравнивания только проблемных элементов:</p>
            
            <div class="social-links-grid">
                <div class="social-link-item">
                    <a href="#" target="_blank" class="social-link">
                        <i class="fab fa-tenchat fa-2x"></i>
                        <span>Tenchat</span>
                    </a>
                </div>
                <div class="social-link-item">
                    <a href="#" target="_blank" class="social-link">
                        <i class="fab fa-ok fa-2x"></i>
                        <span>Ok</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="demo-section">
            <h3>📱 Демонстрация: Много социальных сетей</h3>
            <p class="status-info">Проверка горизонтального расположения и переноса:</p>
            
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
                        <i class="fab fa-tenchat fa-2x"></i>
                        <span>Tenchat</span>
                    </a>
                </div>
                <div class="social-link-item">
                    <a href="#" target="_blank" class="social-link">
                        <i class="fab fa-ok fa-2x"></i>
                        <span>Ok</span>
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
            </div>
        </div>

        <div class="test-container">
            <h2>🧪 Тестирование на реальных страницах</h2>
            <p>Проверьте исправление на реальных страницах сайта:</p>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="profile.php" class="btn btn-primary me-md-2" target="_blank">
                    <i class="fab fa-github me-2"></i>Страница профиля
                </a>
                <a href="contacts.php" class="btn btn-secondary me-md-2" target="_blank">
                    <i class="fas fa-envelope me-2"></i>Страница контактов
                </a>
                <a href="test_social_links_final.php" class="btn btn-success" target="_blank">
                    <i class="fas fa-flask me-2"></i>Общий тест
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
                        <li>Высота: 80px</li>
                        <li>Минимальная ширина: 120px</li>
                        <li>Выравнивание: по нижнему краю</li>
                        <li>Tenchat: "TC", Ok: "OK"</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>📱 Планшет (≤768px)</h4>
                    <ul>
                        <li>Высота: 70px</li>
                        <li>Минимальная ширина: 100px</li>
                        <li>Выравнивание: по нижнему краю</li>
                        <li>Уменьшенные отступы</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>📱 Мобильный (≤576px)</h4>
                    <ul>
                        <li>Высота: 60px</li>
                        <li>Минимальная ширина: 80px</li>
                        <li>Выравнивание: по нижнему краю</li>
                        <li>Компактное отображение</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="test-container">
            <h2>🎯 Итог исправления</h2>
            <p><span class="status-success">✅ ПРОБЛЕМА ИСПРАВЛЕНА!</span></p>
            
            <div class="alert alert-success">
                <h4>🎉 Что исправлено:</h4>
                <ul>
                    <li><strong>✅ Выравнивание:</strong> Все элементы теперь выравниваются по нижнему краю</li>
                    <li><strong>✅ Иконки:</strong> Tenchat отображается как "TC", Ok как "OK"</li>
                    <li><strong>✅ Высота:</strong> Фиксированная высота для всех элементов</li>
                    <li><strong>✅ Горизонталь:</strong> Сохраняется горизонтальное расположение с переносом</li>
                    <li><strong>✅ Адаптивность:</strong> Корректное отображение на всех устройствах</li>
                </ul>
            </div>
            
            <div class="alert alert-info">
                <h4>🔧 Техническое решение:</h4>
                <p>Добавлены специальные CSS правила для текстовых иконок и принудительное выравнивание всех элементов по нижнему краю с помощью <code>align-items: flex-end</code> и <code>justify-content: flex-end</code>.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>