<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

require_once '../config.php';

// Получаем данные из БД
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);

$skills_result = $conn->query("SELECT * FROM skills");
$skills = $skills_result->fetch_all(MYSQLI_ASSOC);

$contact_result = $conn->query("SELECT * FROM contact LIMIT 1");
$contact = $contact_result->fetch_assoc();

$settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_result->fetch_assoc();
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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
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
                                    <button class="btn btn-sm btn-warning edit-project" data-id="<?php echo $project['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-project" data-id="<?php echo $project['id']; ?>">
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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
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
                                <button class="btn btn-sm btn-warning edit-skill" data-id="<?php echo $skill['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-skill" data-id="<?php echo $skill['id']; ?>">
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
                <form id="contactForm" class="admin-form">
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
                <h2 class="mb-4">Настройки сайта</h2>
                <form id="settingsForm" class="admin-form">
                    <input type="hidden" name="id" value="<?php echo $settings['id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название сайта</label>
                                <input type="text" class="form-control" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Заголовок героя</label>
                                <input type="text" class="form-control" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Подзаголовок героя</label>
                                <input type="text" class="form-control" name="hero_subtitle" value="<?php echo htmlspecialchars($settings['hero_subtitle']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL аватара</label>
                                <input type="text" class="form-control" name="avatar" value="<?php echo htmlspecialchars($settings['avatar']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Текст "О себе"</label>
                                <textarea class="form-control" name="about_text" rows="4"><?php echo htmlspecialchars($settings['about_text']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Дизайн</h4>
                            <div class="mb-3">
                                <label class="form-label">Основной цвет</label>
                                <input type="color" class="form-control form-control-color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Вторичный цвет</label>
                                <input type="color" class="form-control form-control-color" name="secondary_color" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Акцентный цвет</label>
                                <input type="color" class="form-control form-control-color" name="accent_color" value="<?php echo htmlspecialchars($settings['accent_color']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Цвет текста</label>
                                <input type="color" class="form-control form-control-color" name="text_color" value="<?php echo htmlspecialchars($settings['text_color']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Цвет фона</label>
                                <input type="color" class="form-control form-control-color" name="bg_color" value="<?php echo htmlspecialchars($settings['bg_color']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Шрифт</label>
                                <select class="form-select" name="font_family">
                                    <option value="Roboto" <?php echo $settings['font_family'] == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                    <option value="Open+Sans" <?php echo $settings['font_family'] == 'Open+Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                    <option value="Montserrat" <?php echo $settings['font_family'] == 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                    <option value="Lato" <?php echo $settings['font_family'] == 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Фоновое изображение (URL)</label>
                                <input type="text" class="form-control" name="bg_image" value="<?php echo htmlspecialchars($settings['bg_image']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Лет опыта</label>
                                <input type="number" class="form-control" name="experience_years" value="<?php echo htmlspecialchars($settings['experience_years']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Проектов выполнено</label>
                                <input type="number" class="form-control" name="projects_count" value="<?php echo htmlspecialchars($settings['projects_count']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Клиентов</label>
                                <input type="number" class="form-control" name="clients_count" value="<?php echo htmlspecialchars($settings['clients_count']); ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                </form>
            </div>
        </main>
    </div>

    <!-- Модальное окно добавления проекта -->
    <div class="modal fade" id="addProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить проект</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProjectForm" enctype="multipart/form-data">
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
                            <div class="form-text">Или URL изображения: <input type="text" class="form-control mt-2" name="image_url" placeholder="https://example.com/image.jpg"></div>
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

    <!-- Модальное окно редактирования проекта -->
    <div class="modal fade" id="editProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать проект</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProjectForm" enctype="multipart/form-data">
                        <input type="hidden" name="id">
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
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <div class="form-text">Или URL изображения: <input type="text" class="form-control mt-2" name="image_url" placeholder="https://example.com/image.jpg"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ссылка на проект</label>
                            <input type="text" class="form-control" name="link" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления навыка -->
    <div class="modal fade" id="addSkillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить навык</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSkillForm">
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

    <!-- Модальное окно редактирования навыка -->
    <div class="modal fade" id="editSkillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать навык</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editSkillForm">
                        <input type="hidden" name="id">
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
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>