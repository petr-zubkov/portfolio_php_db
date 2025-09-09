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
    $name = $_POST['name'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $level = $_POST['level'] ?? 0;
    $id = $_POST['id'] ?? null;
    
    // Валидация
    if (empty($name) || empty($icon) || empty($level)) {
        echo json_encode([
            'success' => false,
            'message' => 'Заполните все поля'
        ]);
        exit;
    }
    
    if ($level < 0 || $level > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Уровень должен быть от 0 до 100'
        ]);
        exit;
    }
    
    if ($id) {
        // Обновление навыка
        $stmt = $conn->prepare("UPDATE skills SET name=?, icon=?, level=? WHERE id=?");
        $stmt->bind_param("ssii", $name, $icon, $level, $id);
        $message = 'Навык успешно обновлен';
    } else {
        // Добавление нового навыка
        $stmt = $conn->prepare("INSERT INTO skills (name, icon, level) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $icon, $level);
        $message = 'Навык успешно добавлен';
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при сохранении навыка'
        ]);
    }
    exit;
}

// Получаем навыки для отображения
$skills_result = $conn->query("SELECT * FROM skills ORDER BY name");
$skills = $skills_result->fetch_all(MYSQLI_ASSOC);

// Редактирование навыка
$edit_skill = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_skill = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_skill ? 'Редактировать навык' : 'Управление навыками'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4"><?php echo $edit_skill ? 'Редактировать навык' : 'Добавить навык'; ?></h2>
            
            <form id="skillForm" class="admin-form">
                <?php if ($edit_skill): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_skill['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название навыка <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($edit_skill['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon" class="form-label">Иконка Font Awesome <span class="text-danger">*</span></label>
                            <select class="form-select" id="icon" name="icon" required>
                                <option value="">Выберите иконку</option>
                                <option value="fas fa-code" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-code' ? 'selected' : ''; ?>>🔧 Код</option>
                                <option value="fas fa-palette" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-palette' ? 'selected' : ''; ?>>🎨 Дизайн</option>
                                <option value="fas fa-camera" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-camera' ? 'selected' : ''; ?>>📷 Камера</option>
                                <option value="fas fa-video" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-video' ? 'selected' : ''; ?>>📹 Видео</option>
                                <option value="fas fa-music" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-music' ? 'selected' : ''; ?>>🎵 Музыка</option>
                                <option value="fas fa-pen" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-pen' ? 'selected' : ''; ?>>✍️ Письмо</option>
                                <option value="fas fa-book" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-book' ? 'selected' : ''; ?>>📚 Книги</option>
                                <option value="fas fa-language" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-language' ? 'selected' : ''; ?>>🌐 Языки</option>
                                <option value="fas fa-chart-line" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-chart-line' ? 'selected' : ''; ?>>📊 Аналитика</option>
                                <option value="fas fa-users" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-users' ? 'selected' : ''; ?>>👥 Команда</option>
                                <option value="fas fa-lightbulb" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-lightbulb' ? 'selected' : ''; ?>>💡 Идеи</option>
                                <option value="fas fa-rocket" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-rocket' ? 'selected' : ''; ?>>🚀 Запуск</option>
                                <option value="fas fa-star" <?php echo ($edit_skill['icon'] ?? '') == 'fas fa-star' ? 'selected' : ''; ?>>⭐ Рейтинг</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="level" class="form-label">Уровень владения (0-100) <span class="text-danger">*</span></label>
                            <input type="range" class="form-range" id="level" name="level" min="0" max="100" 
                                   value="<?php echo htmlspecialchars($edit_skill['level'] ?? 50); ?>" 
                                   oninput="updateLevelDisplay(this.value)">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span id="levelDisplay" class="fw-bold"><?php echo htmlspecialchars($edit_skill['level'] ?? 50); ?>%</span>
                                <span>100%</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> <?php echo $edit_skill ? 'Обновить' : 'Добавить'; ?> навык
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Предпросмотр навыка</h6>
                            </div>
                            <div class="card-body">
                                <div class="skill-card text-center">
                                    <i id="previewIcon" class="<?php echo htmlspecialchars($edit_skill['icon'] ?? 'fas fa-star'); ?> fa-3x mb-3"></i>
                                    <h5 id="previewName"><?php echo htmlspecialchars($edit_skill['name'] ?? 'Название навыка'); ?></h5>
                                    <div class="progress">
                                        <div id="previewProgress" class="progress-bar" style="width: <?php echo htmlspecialchars($edit_skill['level'] ?? 50); ?>%"></div>
                                    </div>
                                    <small class="text-muted" id="previewLevel"><?php echo htmlspecialchars($edit_skill['level'] ?? 50); ?>%</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Доступные иконки</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-code fa-2x"></i>
                                            <br><small>Код</small>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-palette fa-2x"></i>
                                            <br><small>Дизайн</small>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-camera fa-2x"></i>
                                            <br><small>Фото</small>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-video fa-2x"></i>
                                            <br><small>Видео</small>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-music fa-2x"></i>
                                            <br><small>Музыка</small>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2">
                                        <div class="text-center p-2 border rounded">
                                            <i class="fas fa-pen fa-2x"></i>
                                            <br><small>Письмо</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <?php if (!$edit_skill && count($skills) > 0): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Существующие навыки</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($skills as $skill): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="skill-admin-card">
                                        <div class="skill-header">
                                            <i class="<?php echo htmlspecialchars($skill['icon']); ?> fa-2x"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($skill['name']); ?></h6>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" style="width: <?php echo htmlspecialchars($skill['level']); ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($skill['level']); ?>%</small>
                                            </div>
                                        </div>
                                        <div class="skill-actions">
                                            <a href="get_skill.php?edit=<?php echo $skill['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteSkill(<?php echo $skill['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('skillForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            const formData = new FormData(this);
            
            fetch('get_skill.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Произошла ошибка');
                console.error('Error:', error);
            })
            .finally(() => {
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
            
            const form = document.getElementById('skillForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
        
        function deleteSkill(id) {
            if (confirm('Вы уверены, что хотите удалить этот навык?')) {
                fetch('get_skill.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&delete=true`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', data.message);
                    }
                });
            }
        }
        
        function updateLevelDisplay(value) {
            document.getElementById('levelDisplay').textContent = value + '%';
            document.getElementById('previewLevel').textContent = value + '%';
            document.getElementById('previewProgress').style.width = value + '%';
        }
        
        // Обновление предпросмотра
        document.getElementById('name').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value || 'Название навыка';
        });
        
        document.getElementById('icon').addEventListener('change', function() {
            document.getElementById('previewIcon').className = this.value + ' fa-3x mb-3';
        });
    </script>
</body>
</html>