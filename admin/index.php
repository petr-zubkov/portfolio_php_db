<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Получаем статистику
$projects_count = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$skills_count = $conn->query("SELECT COUNT(*) as count FROM skills")->fetch_assoc()['count'];
$themes_count = $conn->query("SELECT COUNT(*) as count FROM themes")->fetch_assoc()['count'];

// Получаем активную тему
$active_theme_result = $conn->query("SELECT * FROM themes WHERE is_active = 1 LIMIT 1");
$active_theme = $active_theme_result->fetch_assoc();
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
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">Панель управления</h2>
            
            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $projects_count; ?></h4>
                                    <p class="card-text">Проектов</p>
                                </div>
                                <div>
                                    <i class="fas fa-folder fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $skills_count; ?></h4>
                                    <p class="card-text">Навыков</p>
                                </div>
                                <div>
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $themes_count; ?></h4>
                                    <p class="card-text">Тем</p>
                                </div>
                                <div>
                                    <i class="fas fa-palette fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Активная тема -->
            <?php if ($active_theme): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Текущая активная тема</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><?php echo htmlspecialchars($active_theme['name']); ?></h6>
                                <p class="text-muted"><?php echo htmlspecialchars($active_theme['description']); ?></p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">Основной: <?php echo $active_theme['primary_color']; ?></span>
                                    <span class="badge bg-secondary">Вторичный: <?php echo $active_theme['secondary_color']; ?></span>
                                    <span class="badge bg-danger">Акцент: <?php echo $active_theme['accent_color']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="theme-preview mb-3" style="height: 80px; border-radius: 5px; display: flex; overflow: hidden;">
                                        <div style="flex: 1; height: 100%; background-color: <?php echo $active_theme['primary_color']; ?>"></div>
                                        <div style="flex: 1; height: 100%; background-color: <?php echo $active_theme['secondary_color']; ?>"></div>
                                        <div style="flex: 1; height: 100%; background-color: <?php echo $active_theme['accent_color']; ?>"></div>
                                    </div>
                                    <div style="font-family: '<?php echo $active_theme['font_family']; ?>', sans-serif; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                        <?php echo htmlspecialchars($active_theme['font_family']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Быстрые действия -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="manage_themes.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-paint-brush"></i><br>
                                Управление темами
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="save_project.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-folder"></i><br>
                                Управление проектами
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="get_skill.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-star"></i><br>
                                Управление навыками
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="save_contact.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-envelope"></i><br>
                                Управление контактами
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Последние проекты -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Последние проекты</h5>
                </div>
                <div class="card-body">
                    <?php
                    $recent_projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
                    if ($recent_projects->num_rows > 0):
                    ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Описание</th>
                                        <th>Дата создания</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($project = $recent_projects->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($project['description'], 0, 50)) . '...'; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($project['created_at'])); ?></td>
                                            <td>
                                                <a href="save_project.php?edit=<?php echo $project['id']; ?>" class="btn btn-sm btn-primary">Редактировать</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Проекты еще не добавлены.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>