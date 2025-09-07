<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_theme'])) {
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $description = $_POST['description'];
        $primary_color = $_POST['primary_color'];
        $secondary_color = $_POST['secondary_color'];
        $accent_color = $_POST['accent_color'];
        $text_color = $_POST['text_color'];
        $bg_color = $_POST['bg_color'];
        $font_family = $_POST['font_family'];
        
        $stmt = $conn->prepare("INSERT INTO themes (name, slug, description, primary_color, secondary_color, accent_color, text_color, bg_color, font_family) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $slug, $description, $primary_color, $secondary_color, $accent_color, $text_color, $bg_color, $font_family);
        $stmt->execute();
        
        $_SESSION['success'] = "Тема успешно добавлена!";
        header("Location: manage_themes.php");
        exit;
    }
    
    if (isset($_POST['update_theme'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $slug = $_POST['slug'];
        $description = $_POST['description'];
        $primary_color = $_POST['primary_color'];
        $secondary_color = $_POST['secondary_color'];
        $accent_color = $_POST['accent_color'];
        $text_color = $_POST['text_color'];
        $bg_color = $_POST['bg_color'];
        $font_family = $_POST['font_family'];
        
        $stmt = $conn->prepare("UPDATE themes SET name=?, slug=?, description=?, primary_color=?, secondary_color=?, accent_color=?, text_color=?, bg_color=?, font_family=? WHERE id=?");
        $stmt->bind_param("sssssssssi", $name, $slug, $description, $primary_color, $secondary_color, $accent_color, $text_color, $bg_color, $font_family, $id);
        $stmt->execute();
        
        $_SESSION['success'] = "Тема успешно обновлена!";
        header("Location: manage_themes.php");
        exit;
    }
    
    if (isset($_POST['activate_theme'])) {
        $id = $_POST['id'];
        
        // Деактивируем все темы
        $conn->query("UPDATE themes SET is_active = 0");
        
        // Активируем выбранную тему
        $stmt = $conn->prepare("UPDATE themes SET is_active = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $_SESSION['success'] = "Тема успешно активирована!";
        header("Location: manage_themes.php");
        exit;
    }
    
    if (isset($_POST['delete_theme'])) {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM themes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $_SESSION['success'] = "Тема успешно удалена!";
        header("Location: manage_themes.php");
        exit;
    }
}

// Получаем все темы
$themes_result = $conn->query("SELECT * FROM themes ORDER BY name");
$themes = $themes_result->fetch_all(MYSQLI_ASSOC);

// Получаем активную тему
$active_theme = null;
foreach ($themes as $theme) {
    if ($theme['is_active']) {
        $active_theme = $theme;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление темами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .theme-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .theme-card.active {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .theme-preview {
            height: 100px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: flex;
            overflow: hidden;
        }
        .color-preview {
            flex: 1;
            height: 100%;
        }
        .font-preview {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">Управление темами</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Активная тема -->
            <?php if ($active_theme): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Текущая активная тема</h5>
                        <a href="sync_theme_settings.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-sync"></i> Синхронизировать настройки
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="theme-preview">
                            <div class="color-preview" style="background-color: <?php echo $active_theme['primary_color']; ?>"></div>
                            <div class="color-preview" style="background-color: <?php echo $active_theme['secondary_color']; ?>"></div>
                            <div class="color-preview" style="background-color: <?php echo $active_theme['accent_color']; ?>"></div>
                        </div>
                        <h6><?php echo htmlspecialchars($active_theme['name']); ?></h6>
                        <p class="text-muted"><?php echo htmlspecialchars($active_theme['description']); ?></p>
                        <div class="font-preview" style="font-family: '<?php echo $active_theme['font_family']; ?>', sans-serif;">
                            Пример текста: Шрифт <?php echo htmlspecialchars($active_theme['font_family']); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Форма добавления новой темы -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Добавить новую тему</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Название темы</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="slug" class="form-label">URL идентификатор</label>
                                    <input type="text" class="form-control" id="slug" name="slug" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="font_family" class="form-label">Шрифт</label>
                                    <select class="form-select" id="font_family" name="font_family" required>
                                        <option value="Roboto">Roboto</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Orbitron">Orbitron</option>
                                        <option value="Open Sans">Open Sans</option>
                                        <option value="Lato">Lato</option>
                                        <option value="Poppins">Poppins</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Основной цвет</label>
                                    <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="#2c3e50" required>
                                </div>
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">Вторичный цвет</label>
                                    <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="#3498db" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="accent_color" class="form-label">Акцентный цвет</label>
                                    <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" value="#e74c3c" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="text_color" class="form-label">Цвет текста</label>
                                    <input type="color" class="form-control form-control-color" id="text_color" name="text_color" value="#333333" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bg_color" class="form-label">Цвет фона</label>
                                    <input type="color" class="form-control form-control-color" id="bg_color" name="bg_color" value="#ffffff" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="add_theme" class="btn btn-primary">Добавить тему</button>
                    </form>
                </div>
            </div>
            
            <!-- Список всех тем -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Все темы</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($themes as $theme): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="theme-card <?php echo $theme['is_active'] ? 'active' : ''; ?>">
                                    <div class="theme-preview">
                                        <div class="color-preview" style="background-color: <?php echo $theme['primary_color']; ?>"></div>
                                        <div class="color-preview" style="background-color: <?php echo $theme['secondary_color']; ?>"></div>
                                        <div class="color-preview" style="background-color: <?php echo $theme['accent_color']; ?>"></div>
                                    </div>
                                    <h6><?php echo htmlspecialchars($theme['name']); ?></h6>
                                    <p class="text-muted small"><?php echo htmlspecialchars($theme['description']); ?></p>
                                    <div class="font-preview" style="font-family: '<?php echo $theme['font_family']; ?>', sans-serif;">
                                        <?php echo htmlspecialchars($theme['font_family']); ?>
                                    </div>
                                    <div class="mt-3">
                                        <?php if (!$theme['is_active']): ?>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $theme['id']; ?>">
                                                <button type="submit" name="activate_theme" class="btn btn-success btn-sm">Активировать</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="../preview_theme.php?theme_id=<?php echo $theme['id']; ?>" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fas fa-eye"></i> Предпросмотр
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $theme['id']; ?>">
                                            Редактировать
                                        </button>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $theme['id']; ?>">
                                            <button type="submit" name="delete_theme" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту тему?')">Удалить</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Модальное окно редактирования -->
                            <div class="modal fade" id="editModal<?php echo $theme['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Редактировать тему</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" name="id" value="<?php echo $theme['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="name<?php echo $theme['id']; ?>" class="form-label">Название темы</label>
                                                    <input type="text" class="form-control" id="name<?php echo $theme['id']; ?>" name="name" value="<?php echo htmlspecialchars($theme['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="slug<?php echo $theme['id']; ?>" class="form-label">URL идентификатор</label>
                                                    <input type="text" class="form-control" id="slug<?php echo $theme['id']; ?>" name="slug" value="<?php echo htmlspecialchars($theme['slug']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description<?php echo $theme['id']; ?>" class="form-label">Описание</label>
                                                    <textarea class="form-control" id="description<?php echo $theme['id']; ?>" name="description" rows="3" required><?php echo htmlspecialchars($theme['description']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="font_family<?php echo $theme['id']; ?>" class="form-label">Шрифт</label>
                                                    <select class="form-select" id="font_family<?php echo $theme['id']; ?>" name="font_family" required>
                                                        <option value="Roboto" <?php echo $theme['font_family'] == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                                        <option value="Montserrat" <?php echo $theme['font_family'] == 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                                        <option value="Orbitron" <?php echo $theme['font_family'] == 'Orbitron' ? 'selected' : ''; ?>>Orbitron</option>
                                                        <option value="Open Sans" <?php echo $theme['font_family'] == 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                                        <option value="Lato" <?php echo $theme['font_family'] == 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                                        <option value="Poppins" <?php echo $theme['font_family'] == 'Poppins' ? 'selected' : ''; ?>>Poppins</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="primary_color<?php echo $theme['id']; ?>" class="form-label">Основной цвет</label>
                                                            <input type="color" class="form-control form-control-color" id="primary_color<?php echo $theme['id']; ?>" name="primary_color" value="<?php echo $theme['primary_color']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="secondary_color<?php echo $theme['id']; ?>" class="form-label">Вторичный цвет</label>
                                                            <input type="color" class="form-control form-control-color" id="secondary_color<?php echo $theme['id']; ?>" name="secondary_color" value="<?php echo $theme['secondary_color']; ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="accent_color<?php echo $theme['id']; ?>" class="form-label">Акцентный цвет</label>
                                                            <input type="color" class="form-control form-control-color" id="accent_color<?php echo $theme['id']; ?>" name="accent_color" value="<?php echo $theme['accent_color']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="text_color<?php echo $theme['id']; ?>" class="form-label">Цвет текста</label>
                                                            <input type="color" class="form-control form-control-color" id="text_color<?php echo $theme['id']; ?>" name="text_color" value="<?php echo $theme['text_color']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="bg_color<?php echo $theme['id']; ?>" class="form-label">Цвет фона</label>
                                                            <input type="color" class="form-control form-control-color" id="bg_color<?php echo $theme['id']; ?>" name="bg_color" value="<?php echo $theme['bg_color']; ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" name="update_theme" class="btn btn-primary">Сохранить изменения</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>