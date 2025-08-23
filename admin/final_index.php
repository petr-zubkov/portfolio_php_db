<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

require_once '../config.php';

// Получаем данные из БД
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $projects_result ? $projects_result->fetch_all(MYSQLI_ASSOC) : [];

$skills_result = $conn->query("SELECT * FROM skills");
$skills = $skills_result ? $skills_result->fetch_all(MYSQLI_ASSOC) : [];

$contact_result = $conn->query("SELECT * FROM contact LIMIT 1");
$contact = $contact_result ? $contact_result->fetch_assoc() : ['email' => '', 'phone' => '', 'telegram' => ''];

$settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_result ? $settings_result->fetch_assoc() : [
    'site_title' => 'Портфолио верстальщика книг',
    'hero_title' => 'Верстальщик книг',
    'hero_subtitle' => 'Профессиональная верстка печатных и электронных изданий',
    'avatar' => '../assets/img/placeholder.jpg',
    'about_text' => '',
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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Сайдбар -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Админ-панель</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-tab="projects">
                        <i class="fas fa-folder"></i> Проекты
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-tab="skills">
                        <i class="fas fa-tools"></i> Навыки
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-tab="contact">
                        <i class="fas fa-address-book"></i> Контакты
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-tab="settings">
                        <i class="fas fa-cog"></i> Настройки
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_logs.php">
                        <i class="fas fa-file-alt"></i> Логи ошибок
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Выход
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Основной контент -->
        <main class="admin-main">
            <!-- Проекты -->
            <div id="projects-tab" class="tab-content active">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Управление проектами</h2>
                    <button class="btn btn-primary" onclick="showAddProjectModal()">
                        <i class="fas fa-plus"></i> Добавить проект
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Изображение</th>
                                <th>Название</th>
                                <th>Описание</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($project['image']); ?>" alt="" width="50"></td>
                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editProject(<?php echo $project['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Навыки -->
            <div id="skills-tab" class="tab-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Управление навыками</h2>
                    <button class="btn btn-primary" onclick="showAddSkillModal()">
                        <i class="fas fa-plus"></i> Добавить навык
                    </button>
                </div>
                
                <div class="row">
                    <?php foreach ($skills as $skill): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="skill-admin-card">
                            <div class="skill-header">
                                <i class="<?php echo htmlspecialchars($skill['icon']); ?> fa-2x"></i>
                                <h5><?php echo htmlspecialchars($skill['name']); ?></h5>
                            </div>
                            <div class="skill-level">
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo (int)$skill['level']; ?>%">
                                        <?php echo (int)$skill['level']; ?>%
                                    </div>
                                </div>
                            </div>
                            <div class="skill-actions">
                                <button class="btn btn-sm btn-warning" onclick="editSkill(<?php echo $skill['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteSkill(<?php echo $skill['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Контакты -->
            <div id="contact-tab" class="tab-content">
                <h2 class="mb-4">Управление контактами</h2>
                <form id="contactForm" class="admin-form" onsubmit="saveContact(event)">
                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($contact['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telegram</label>
                        <input type="text" class="form-control" name="telegram" value="<?php echo htmlspecialchars($contact['telegram']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>

            <!-- Настройки -->
            <div id="settings-tab" class="tab-content">
                <h2 class="mb-4">Управление настройками сайта</h2>
                <form id="settingsForm" class="admin-form" onsubmit="saveSettings(event)">
                    <input type="hidden" name="id" value="<?php echo isset($settings['id']) ? $settings['id'] : '1'; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название сайта</label>
                                <input type="text" class="form-control" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Заголовок героя</label>
                                <input type="text" class="form-control" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Подзаголовок героя</label>
                                <input type="text" class="form-control" name="hero_subtitle" value="<?php echo htmlspecialchars($settings['hero_subtitle']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Лет опыта</label>
                                <input type="number" class="form-control" name="experience_years" value="<?php echo htmlspecialchars($settings['experience_years']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Проектов выполнено</label>
                                <input type="number" class="form-control" name="projects_count" value="<?php echo htmlspecialchars($settings['projects_count']; ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Клиентов</label>
                                <input type="number" class="form-control" name="clients_count" value="<?php echo htmlspecialchars($settings['clients_count']; ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </main>
    </div>

    <!-- Модальные окна (оставляем как в debug версии) -->
    <div class="modal fade" id="addProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить проект</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProjectForm" onsubmit="addProject(event)" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Изображение</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ссылка на проект</label>
                            <input type="text" class="form-control" name="link" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSkillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить навык</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSkillForm" onsubmit="addSkill(event)">
                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Иконка (Font Awesome класс)</label>
                            <input type="text" class="form-control" name="icon" placeholder="fas fa-book" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Уровень (%)</label>
                            <input type="number" class="form-control" name="level" min="0" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Наш JavaScript -->
    <script>
    // Функция для показа уведомлений
    function showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        const style = document.createElement('style');
        style.textContent = `
            .notification-toast {
                position: fixed;
                top: 20px;
                right: 20px;
                min-width: 300px;
                padding: 15px;
                border-radius: 5px;
                color: white;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .notification-success { background-color: #28a745; }
            .notification-error { background-color: #dc3545; }
            .notification-info { background-color: #17a2b8; }
            .notification-content { display: flex; align-items: center; gap: 10px; }
            .notification-close {
                position: absolute;
                top: 5px;
                right: 10px;
                background: none;
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                opacity: 0.7;
            }
            .notification-close:hover { opacity: 1; }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        
        if (!document.querySelector('#notification-styles')) {
            style.id = 'notification-styles';
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Переключение вкладок
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM загружен');
        
        const tabLinks = document.querySelectorAll('.admin-sidebar .nav-link[data-tab]');
        tabLinks.forEach(link => {
            console.log('Добавляем обработчик для вкладки:', link.getAttribute('data-tab'));
            link.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Клик по вкладке:', this.getAttribute('data-tab'));
                
                document.querySelectorAll('.admin-sidebar .nav-link').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                
                this.classList.add('active');
                
                const tabId = this.getAttribute('data-tab') + '-tab';
                const tabElement = document.getElementById(tabId);
                if (tabElement) {
                    tabElement.classList.add('active');
                    console.log('Активирована вкладка:', tabId);
                } else {
                    console.error('Вкладка не найдена:', tabId);
                }
            });
        });
        
        console.log('Обработчики вкладок добавлены');
    });

    // Функции для работы с проектами
    function showAddProjectModal() {
        console.log('Показываем модальное окно добавления проекта');
        const modal = new bootstrap.Modal(document.getElementById('addProjectModal'));
        modal.show();
    }

    function addProject(event) {
        event.preventDefault();
        console.log('Добавляем проект');
        
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('save_project.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Проект успешно добавлен!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Ошибка: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Ошибка запроса: ' + error.message, 'error');
        });
    }

    function editProject(id) {
        console.log('Редактируем проект:', id);
        showNotification('Функция редактирования проекта в разработке', 'info');
    }

    function deleteProject(id) {
        console.log('Удаляем проект:', id);
        if (confirm('Вы уверены, что хотите удалить этот проект?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('save_project.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Проект удален!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Ошибка удаления', 'error');
                }
            })
            .catch(error => {
                showNotification('Ошибка запроса: ' + error.message, 'error');
            });
        }
    }

    // Функции для работы с навыками
    function showAddSkillModal() {
        console.log('Показываем модальное окно добавления навыка');
        const modal = new bootstrap.Modal(document.getElementById('addSkillModal'));
        modal.show();
    }

    function addSkill(event) {
        event.preventDefault();
        console.log('Добавляем навык');
        
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('save_skills.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Навык успешно добавлен!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Ошибка: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Ошибка запроса: ' + error.message, 'error');
        });
    }

    function editSkill(id) {
        console.log('Редактируем навык:', id);
        showNotification('Функция редактирования навыка в разработке', 'info');
    }

    function deleteSkill(id) {
        console.log('Удаляем навык:', id);
        if (confirm('Вы уверены, что хотите удалить этот навык?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('save_skills.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Навык удален!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Ошибка удаления', 'error');
                }
            })
            .catch(error => {
                showNotification('Ошибка запроса: ' + error.message, 'error');
            });
        }
    }

    // Функция сохранения контактов
    function saveContact(event) {
        event.preventDefault();
        console.log('Сохраняем контакты');
        
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('save_contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Контакты успешно сохранены!', 'success');
            } else {
                showNotification('Ошибка: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Ошибка запроса: ' + error.message, 'error');
        });
    }

    // Функция сохранения настроек
    function saveSettings(event) {
        event.preventDefault();
        console.log('Сохраняем настройки');
        
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
        
        fetch('save_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showNotification('Настройки успешно сохранены!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Ошибка: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка запроса: ' + error.message, 'error');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    }

    console.log('JavaScript загружен');
    </script>
</body>
</html>
<?php
$conn->close();
?>