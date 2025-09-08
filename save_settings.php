<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Получаем текущие настройки
try {
    $settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
    $settings = $settings_result->fetch_assoc();
} catch (Exception $e) {
    // Если таблица не существует, создаем ее с настройками по умолчанию
    $settings = [
        'site_title' => 'Мое портфолио',
        'hero_title' => 'Верстальщик',
        'hero_subtitle' => 'Профессиональная верстка',
        'avatar' => 'assets/img/placeholder.jpg',
        'about_text' => 'Опытный верстальщик с многолетним стажем работы.',
        'primary_color' => '#2c3e50',
        'secondary_color' => '#3498db',
        'accent_color' => '#e74c3c',
        'text_color' => '#333333',
        'bg_color' => '#ffffff',
        'font_family' => 'Roboto',
        'bg_image' => '',
        'experience_years' => 5,
        'projects_count' => 100,
        'clients_count' => 50
    ];
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = $_POST['site_title'] ?? '';
    $hero_title = $_POST['hero_title'] ?? '';
    $hero_subtitle = $_POST['hero_subtitle'] ?? '';
    $avatar = $_POST['avatar'] ?? '';
    $about_text = $_POST['about_text'] ?? '';
    $primary_color = $_POST['primary_color'] ?? '#2c3e50';
    $secondary_color = $_POST['secondary_color'] ?? '#3498db';
    $accent_color = $_POST['accent_color'] ?? '#e74c3c';
    $text_color = $_POST['text_color'] ?? '#333333';
    $bg_color = $_POST['bg_color'] ?? '#ffffff';
    $font_family = $_POST['font_family'] ?? 'Roboto';
    $bg_image = $_POST['bg_image'] ?? '';
    $experience_years = $_POST['experience_years'] ?? 5;
    $projects_count = $_POST['projects_count'] ?? 100;
    $clients_count = $_POST['clients_count'] ?? 50;
    
    // Валидация обязательных полей
    if (empty($site_title)) {
        echo json_encode([
            'success' => false,
            'message' => 'Название сайта обязательно'
        ]);
        exit;
    }
    
    if (empty($hero_title)) {
        echo json_encode([
            'success' => false,
            'message' => 'Заголовок героя обязателен'
        ]);
        exit;
    }
    
    // Обновление настроек
    try {
        // Проверяем, существует ли таблица
        $check_table = $conn->query("SHOW TABLES LIKE 'settings'");
        if ($check_table->num_rows == 0) {
            // Создаем таблицу
            $create_table = "CREATE TABLE settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                site_title VARCHAR(255) NOT NULL,
                hero_title VARCHAR(255) NOT NULL,
                hero_subtitle TEXT,
                avatar VARCHAR(255),
                about_text TEXT,
                primary_color VARCHAR(7) NOT NULL,
                secondary_color VARCHAR(7) NOT NULL,
                accent_color VARCHAR(7) NOT NULL,
                text_color VARCHAR(7) NOT NULL,
                bg_color VARCHAR(7) NOT NULL,
                font_family VARCHAR(50) NOT NULL,
                bg_image VARCHAR(255),
                experience_years INT NOT NULL,
                projects_count INT NOT NULL,
                clients_count INT NOT NULL
            )";
            $conn->query($create_table);
            
            // Вставляем первую запись
            $insert_query = "INSERT INTO settings (site_title, hero_title, hero_subtitle, avatar, about_text, primary_color, secondary_color, accent_color, text_color, bg_color, font_family, bg_image, experience_years, projects_count, clients_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssssssssssssiii", 
                $site_title, $hero_title, $hero_subtitle, $avatar, $about_text,
                $primary_color, $secondary_color, $accent_color, $text_color, $bg_color,
                $font_family, $bg_image, $experience_years, $projects_count, $clients_count
            );
            $stmt->execute();
        } else {
            // Обновляем существующую запись
            $update_query = "UPDATE settings SET 
                site_title = ?,
                hero_title = ?,
                hero_subtitle = ?,
                avatar = ?,
                about_text = ?,
                primary_color = ?,
                secondary_color = ?,
                accent_color = ?,
                text_color = ?,
                bg_color = ?,
                font_family = ?,
                bg_image = ?,
                experience_years = ?,
                projects_count = ?,
                clients_count = ?
            WHERE id = 1";
            
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssssssssssiii", 
                $site_title, $hero_title, $hero_subtitle, $avatar, $about_text,
                $primary_color, $secondary_color, $accent_color, $text_color, $bg_color,
                $font_family, $bg_image, $experience_years, $projects_count, $clients_count
            );
            $stmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Настройки успешно сохранены'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при сохранении настроек: ' . $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки сайта</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .color-preview {
            height: 40px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        .font-preview {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ddd;
            margin-top: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">Настройки сайта</h2>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Здесь вы можете настроить основные параметры сайта. Для управления темами используйте <a href="manage_themes.php">Управление темами</a>.
            </div>
            
            <form id="settingsForm" class="admin-form">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Основные настройки</h5>
                        
                        <div class="mb-3">
                            <label for="site_title" class="form-label">Название сайта <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_title" name="site_title" 
                                   value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hero_title" class="form-label">Заголовок героя <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hero_title" name="hero_title" 
                                   value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hero_subtitle" class="form-label">Подзаголовок героя</label>
                            <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" rows="2"><?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="avatar" class="form-label">URL аватара</label>
                            <input type="url" class="form-control" id="avatar" name="avatar" 
                                   value="<?php echo htmlspecialchars($settings['avatar'] ?? ''); ?>" 
                                   onchange="updateAvatarPreview(this.value)">
                            <div id="avatarPreview" class="avatar-preview">
                                <img src="<?php echo htmlspecialchars($settings['avatar'] ?? '../assets/img/placeholder.jpg'); ?>" 
                                     alt="Аватар" id="avatarImg" onerror="this.src='../assets/img/placeholder.jpg'">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="about_text" class="form-label">Текст "О себе"</label>
                            <textarea class="form-control" id="about_text" name="about_text" rows="4"><?php echo htmlspecialchars($settings['about_text'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">Статистика</h5>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="experience_years" class="form-label">Лет опыта</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                           value="<?php echo htmlspecialchars($settings['experience_years'] ?? 5); ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="projects_count" class="form-label">Проектов</label>
                                    <input type="number" class="form-control" id="projects_count" name="projects_count" 
                                           value="<?php echo htmlspecialchars($settings['projects_count'] ?? 100); ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clients_count" class="form-label">Клиентов</label>
                                    <input type="number" class="form-control" id="clients_count" name="clients_count" 
                                           value="<?php echo htmlspecialchars($settings['clients_count'] ?? 50); ?>" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3 mt-4">Дизайн</h5>
                        
                        <div class="mb-3">
                            <label for="font_family" class="form-label">Шрифт</label>
                            <select class="form-select" id="font_family" name="font_family" 
                                    onchange="updateFontPreview(this.value)">
                                <option value="Roboto" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                <option value="Montserrat" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                <option value="Orbitron" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Orbitron' ? 'selected' : ''; ?>>Orbitron</option>
                                <option value="Open Sans" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                <option value="Lato" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                <option value="Poppins" <?php echo ($settings['font_family'] ?? 'Roboto') == 'Poppins' ? 'selected' : ''; ?>>Poppins</option>
                            </select>
                            <div id="fontPreview" class="font-preview" style="font-family: '<?php echo $settings['font_family'] ?? 'Roboto'; ?>', sans-serif;">
                                Пример текста: Шрифт <?php echo htmlspecialchars($settings['font_family'] ?? 'Roboto'); ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Основной цвет</label>
                                    <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" 
                                           value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#2c3e50'); ?>" 
                                           onchange="updateColorPreview()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">Вторичный цвет</label>
                                    <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" 
                                           value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#3498db'); ?>" 
                                           onchange="updateColorPreview()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="accent_color" class="form-label">Акцентный цвет</label>
                                    <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" 
                                           value="<?php echo htmlspecialchars($settings['accent_color'] ?? '#e74c3c'); ?>" 
                                           onchange="updateColorPreview()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="text_color" class="form-label">Цвет текста</label>
                                    <input type="color" class="form-control form-control-color" id="text_color" name="text_color" 
                                           value="<?php echo htmlspecialchars($settings['text_color'] ?? '#333333'); ?>" 
                                           onchange="updateColorPreview()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bg_color" class="form-label">Цвет фона</label>
                                    <input type="color" class="form-control form-control-color" id="bg_color" name="bg_color" 
                                           value="<?php echo htmlspecialchars($settings['bg_color'] ?? '#ffffff'); ?>" 
                                           onchange="updateColorPreview()">
                                </div>
                            </div>
                        </div>
                        
                        <div id="colorPreview" class="color-preview" style="display: flex; height: 60px;">
                            <div style="flex: 1; background-color: <?php echo htmlspecialchars($settings['primary_color'] ?? '#2c3e50'); ?>"></div>
                            <div style="flex: 1; background-color: <?php echo htmlspecialchars($settings['secondary_color'] ?? '#3498db'); ?>"></div>
                            <div style="flex: 1; background-color: <?php echo htmlspecialchars($settings['accent_color'] ?? '#e74c3c'); ?>"></div>
                        </div>
                        
                        <div class="mb-3 mt-3">
                            <label for="bg_image" class="form-label">Фоновое изображение (URL)</label>
                            <input type="url" class="form-control" id="bg_image" name="bg_image" 
                                   value="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>"
                                   placeholder="https://example.com/image.jpg">
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save"></i> Сохранить настройки
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;
            
            // Показываем индикатор загрузки
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            // Собираем данные формы
            const formData = new FormData(this);
            
            // Отправляем AJAX запрос
            fetch('save_settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Произошла ошибка при отправке запроса');
                console.error('Error:', error);
            })
            .finally(() => {
                // Восстанавливаем кнопку
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        });
        
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('settingsForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
        
        function updateColorPreview() {
            const primary = document.getElementById('primary_color').value;
            const secondary = document.getElementById('secondary_color').value;
            const accent = document.getElementById('accent_color').value;
            
            const preview = document.getElementById('colorPreview');
            preview.innerHTML = `
                <div style="flex: 1; background-color: ${primary}"></div>
                <div style="flex: 1; background-color: ${secondary}"></div>
                <div style="flex: 1; background-color: ${accent}"></div>
            `;
        }
        
        function updateFontPreview(fontFamily) {
            const preview = document.getElementById('fontPreview');
            preview.style.fontFamily = `'${fontFamily}', sans-serif`;
            preview.textContent = `Пример текста: Шрифт ${fontFamily}`;
        }
        
        function updateAvatarPreview(url) {
            const img = document.getElementById('avatarImg');
            if (url) {
                img.src = url;
                img.onerror = function() {
                    this.src = '../assets/img/placeholder.jpg';
                };
            } else {
                img.src = '../assets/img/placeholder.jpg';
            }
        }
// Функция для преобразования относительных путей в абсолютные перед отправкой формы
function processFormBeforeSubmit() {
    const avatarInput = document.getElementById('avatar');
    const bgImageInput = document.getElementById('bg_image');
    const siteUrl = 'https://zubkov.space';
    
    // Обрабатываем поле аватара
    if (avatarInput.value) {
        if (!avatarInput.value.startsWith('http://') && !avatarInput.value.startsWith('https://')) {
            // Преобразуем относительный путь в абсолютный
            avatarInput.value = siteUrl + '/' + avatarInput.value.replace(/^\/+/, '');
        }
    }
    
    // Обрабатываем поле фонового изображения
    if (bgImageInput.value) {
        if (!bgImageInput.value.startsWith('http://') && !bgImageInput.value.startsWith('https://')) {
            // Преобразуем относительный путь в абсолютный
            bgImageInput.value = siteUrl + '/' + bgImageInput.value.replace(/^\/+/, '');
        }
    }
}

// Добавляем обработчик на отправку формы
document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
    // Обрабатываем поля перед отправкой
    processFormBeforeSubmit();
});
    </script>
</body>
</html>