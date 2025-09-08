<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Получаем текущую персональную информацию
try {
    $personal_info_result = $conn->query("SELECT * FROM personal_info LIMIT 1");
    $personal_info = $personal_info_result->fetch_assoc();
    
    if (!$personal_info) {
        // Если нет записи, создаем с настройками по умолчанию
        $default_social_links = json_encode([
            'github' => '',
            'linkedin' => '',
            'twitter' => '',
            'website' => ''
        ]);
        
        $insert_query = "INSERT INTO personal_info (full_name, profession, bio, avatar, email, phone, telegram, location, experience_years, projects_count, clients_count, social_links) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $default_full_name = 'Ваше имя';
        $default_profession = 'Ваша профессия';
        $default_bio = 'Расскажите о себе...';
        $default_avatar = '../assets/img/placeholder.jpg';
        $default_email = 'your.email@example.com';
        $default_phone = '+7 (999) 123-45-67';
        $default_telegram = '@username';
        $default_location = 'Ваш город';
        $default_experience = 0;
        $default_projects = 0;
        $default_clients = 0;
        
        $stmt->bind_param("sssssssssiis", 
            $default_full_name, $default_profession, $default_bio, $default_avatar,
            $default_email, $default_phone, $default_telegram, $default_location,
            $default_experience, $default_projects, $default_clients, $default_social_links
        );
        $stmt->execute();
        
        // Получаем только что созданную запись
        $personal_info_result = $conn->query("SELECT * FROM personal_info LIMIT 1");
        $personal_info = $personal_info_result->fetch_assoc();
    }
    
    // Декодируем социальные ссылки
    $social_links = json_decode($personal_info['social_links'] ?: '{}', true);
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Ошибка при получении персональной информации: " . $e->getMessage() . "</div>";
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $avatar = $_POST['avatar'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $telegram = $_POST['telegram'] ?? '';
    $location = $_POST['location'] ?? '';
    $experience_years = $_POST['experience_years'] ?? 0;
    $projects_count = $_POST['projects_count'] ?? 0;
    $clients_count = $_POST['clients_count'] ?? 0;
    
    // Социальные ссылки
    $social_links_data = [
        'github' => $_POST['github'] ?? '',
        'linkedin' => $_POST['linkedin'] ?? '',
        'twitter' => $_POST['twitter'] ?? '',
        'website' => $_POST['website'] ?? ''
    ];
    $social_links_json = json_encode($social_links_data);
    
    // Валидация обязательных полей
    if (empty($full_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Полное имя обязательно'
        ]);
        exit;
    }
    
    if (empty($profession)) {
        echo json_encode([
            'success' => false,
            'message' => 'Профессия обязательна'
        ]);
        exit;
    }
    
    // Обновление персональной информации
    try {
        $update_query = "UPDATE personal_info SET 
            full_name = ?,
            profession = ?,
            bio = ?,
            avatar = ?,
            email = ?,
            phone = ?,
            telegram = ?,
            location = ?,
            experience_years = ?,
            projects_count = ?,
            clients_count = ?,
            social_links = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssssiissi", 
            $full_name, $profession, $bio, $avatar, $email, $phone, $telegram,
            $location, $experience_years, $projects_count, $clients_count,
            $social_links_json, $personal_info['id']
        );
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Персональная информация успешно обновлена'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при обновлении персональной информации: ' . $e->getMessage()
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
    <title>Управление персональной информацией</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .avatar-preview {
            width: 120px;
            height: 120px;
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
        .social-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">Управление персональной информацией</h2>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Здесь вы можете управлять вашей персональной информацией, которая будет отображаться на сайте. Эта информация отделена от тем оформления.
            </div>
            
            <form id="personalInfoForm" class="admin-form">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-3">Основная информация</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label required-field">Полное имя</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($personal_info['full_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profession" class="form-label required-field">Профессия</label>
                                    <input type="text" class="form-control" id="profession" name="profession" 
                                           value="<?php echo htmlspecialchars($personal_info['profession'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">О себе</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Расскажите о себе, своем опыте и навыках..."><?php echo htmlspecialchars($personal_info['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Местоположение</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?php echo htmlspecialchars($personal_info['location'] ?? ''); ?>"
                                   placeholder="Город, страна">
                        </div>
                        
                        <h5 class="mb-3 mt-4">Контактная информация</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($personal_info['email'] ?? ''); ?>"
                                           placeholder="your.email@example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($personal_info['phone'] ?? ''); ?>"
                                           placeholder="+7 (999) 123-45-67">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telegram" class="form-label">Telegram</label>
                            <input type="text" class="form-control" id="telegram" name="telegram" 
                                   value="<?php echo htmlspecialchars($personal_info['telegram'] ?? ''); ?>"
                                   placeholder="@username">
                        </div>
                        
                        <h5 class="mb-3 mt-4">Статистика</h5>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="experience_years" class="form-label">Лет опыта</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                           value="<?php echo htmlspecialchars($personal_info['experience_years'] ?? 0); ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="projects_count" class="form-label">Проектов</label>
                                    <input type="number" class="form-control" id="projects_count" name="projects_count" 
                                           value="<?php echo htmlspecialchars($personal_info['projects_count'] ?? 0); ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clients_count" class="form-label">Клиентов</label>
                                    <input type="number" class="form-control" id="clients_count" name="clients_count" 
                                           value="<?php echo htmlspecialchars($personal_info['clients_count'] ?? 0); ?>" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3 mt-4">Социальные сети</h5>
                        
                        <div class="social-links-grid">
                            <div class="mb-3">
                                <label for="github" class="form-label">GitHub</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-github"></i></span>
                                    <input type="url" class="form-control" id="github" name="github" 
                                           value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>" 
                                           placeholder="https://github.com/username">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                    <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                           value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>" 
                                           placeholder="https://linkedin.com/in/username">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="twitter" class="form-label">Twitter</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                    <input type="url" class="form-control" id="twitter" name="twitter" 
                                           value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>" 
                                           placeholder="https://twitter.com/username">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="website" class="form-label">Веб-сайт</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="<?php echo htmlspecialchars($social_links['website'] ?? ''); ?>" 
                                           placeholder="https://yourwebsite.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <h5 class="mb-3">Аватар</h5>
                        
                        <div class="mb-3">
                            <label for="avatar" class="form-label">URL аватара</label>
                            <input type="url" class="form-control" id="avatar" name="avatar" 
                                   value="<?php echo htmlspecialchars($personal_info['avatar'] ?? ''); ?>" 
                                   onchange="updateAvatarPreview(this.value)"
                                   placeholder="https://example.com/avatar.jpg">
                            <div id="avatarPreview" class="avatar-preview">
                                <img src="<?php echo htmlspecialchars($personal_info['avatar'] ?? '../assets/img/placeholder.jpg'); ?>" 
                                     alt="Аватар" id="avatarImg" onerror="this.src='../assets/img/placeholder.jpg'">
                            </div>
                            <small class="text-muted">Поддерживаются изображения в формате JPG, PNG, GIF</small>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Предпросмотр</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="avatar-preview mx-auto mb-3">
                                        <img src="<?php echo htmlspecialchars($personal_info['avatar'] ?? '../assets/img/placeholder.jpg'); ?>" 
                                             alt="Аватар" id="previewAvatarImg" onerror="this.src='../assets/img/placeholder.jpg'">
                                    </div>
                                    <h5 id="previewName"><?php echo htmlspecialchars($personal_info['full_name'] ?? 'Ваше имя'); ?></h5>
                                    <p class="text-muted" id="previewProfession"><?php echo htmlspecialchars($personal_info['profession'] ?? 'Ваша профессия'); ?></p>
                                    <p class="small text-muted" id="previewLocation">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo htmlspecialchars($personal_info['location'] ?? 'Ваш город'); ?>
                                    </p>
                                    <div class="mt-2">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($personal_info['experience_years'] ?? 0); ?>+ лет</span>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($personal_info['projects_count'] ?? 0); ?>+ проектов</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-light mt-3">
                            <h6><i class="fas fa-lightbulb"></i> Совет</h6>
                            <p class="small mb-0">Используйте квадратное изображение для аватара (рекомендуемый размер: 400x400px). Это обеспечит лучшее отображение на всех устройствах.</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Обновление превью аватара
        function updateAvatarPreview(url) {
            const img = document.getElementById('avatarImg');
            const previewImg = document.getElementById('previewAvatarImg');
            
            if (url) {
                img.src = url;
                previewImg.src = url;
            } else {
                img.src = '../assets/img/placeholder.jpg';
                previewImg.src = '../assets/img/placeholder.jpg';
            }
        }
        
        // Обновление превью текста
        function updateTextPreview() {
            const name = document.getElementById('full_name').value || 'Ваше имя';
            const profession = document.getElementById('profession').value || 'Ваша профессия';
            const location = document.getElementById('location').value || 'Ваш город';
            
            document.getElementById('previewName').textContent = name;
            document.getElementById('previewProfession').textContent = profession;
            document.getElementById('previewLocation').innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + location;
        }
        
        // Добавляем обработчики событий для обновления превью
        document.getElementById('full_name').addEventListener('input', updateTextPreview);
        document.getElementById('profession').addEventListener('input', updateTextPreview);
        document.getElementById('location').addEventListener('input', updateTextPreview);
        
        // Обработка формы
        document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Показываем индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            submitBtn.disabled = true;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Показываем уведомление об успехе
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.admin-main').insertBefore(alertDiv, document.querySelector('.admin-main').firstChild);
                    
                    // Автоматически скрываем уведомление через 5 секунд
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                } else {
                    // Показываем уведомление об ошибке
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.admin-main').insertBefore(alertDiv, document.querySelector('.admin-main').firstChild);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i> Произошла ошибка при сохранении
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.admin-main').insertBefore(alertDiv, document.querySelector('.admin-main').firstChild);
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>