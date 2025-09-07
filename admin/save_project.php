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
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_POST['image'] ?? '';
    $link = $_POST['link'] ?? '';
    $id = $_POST['id'] ?? null;
    
    // Валидация
    if (empty($title) || empty($description) || empty($image)) {
        echo json_encode([
            'success' => false,
            'message' => 'Заполните все обязательные поля'
        ]);
        exit;
    }
    
    if ($id) {
        // Обновление проекта
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, image=?, link=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $description, $image, $link, $id);
        $message = 'Проект успешно обновлен';
    } else {
        // Добавление нового проекта
        $stmt = $conn->prepare("INSERT INTO projects (title, description, image, link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $image, $link);
        $message = 'Проект успешно добавлен';
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при сохранении проекта'
        ]);
    }
    exit;
}

// Получаем проекты для отображения
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $projects_result->fetch_all(MYSQLI_ASSOC);

// Редактирование проекта
$edit_project = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_project = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_project ? 'Редактировать проект' : 'Управление проектами'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4"><?php echo $edit_project ? 'Редактировать проект' : 'Добавить проект'; ?></h2>
            
            <form id="projectForm" class="admin-form">
                <?php if ($edit_project): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Название проекта <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($edit_project['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($edit_project['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">URL изображения <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="image" name="image" 
                                           value="<?php echo htmlspecialchars($edit_project['image'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="link" class="form-label">Ссылка на проект</label>
                                    <input type="url" class="form-control" id="link" name="link" 
                                           value="<?php echo htmlspecialchars($edit_project['link'] ?? '#'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> <?php echo $edit_project ? 'Обновить' : 'Добавить'; ?> проект
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Предпросмотр</h6>
                            </div>
                            <div class="card-body">
                                <div class="portfolio-card mb-3" style="height: 200px;">
                                    <img id="previewImage" src="<?php echo htmlspecialchars($edit_project['image'] ?? '../assets/img/placeholder.jpg'); ?>" 
                                         alt="Предпросмотр" style="width: 100%; height: 100%; object-fit: cover;"
                                         onerror="this.src='../assets/img/placeholder.jpg'">
                                </div>
                                <h6 id="previewTitle"><?php echo htmlspecialchars($edit_project['title'] ?? 'Название проекта'); ?></h6>
                                <p class="small text-muted" id="previewDescription"><?php echo htmlspecialchars(substr($edit_project['description'] ?? 'Описание проекта', 0, 100)) . '...'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <?php if (!$edit_project && count($projects) > 0): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Существующие проекты</h5>
                    </div>
                    <div class="card-body">
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
                                    <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($project['description'], 0, 50)) . '...'; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($project['created_at'])); ?></td>
                                            <td>
                                                <a href="save_project.php?edit=<?php echo $project['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            const formData = new FormData(this);
            
            fetch('save_project.php', {
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
            
            const form = document.getElementById('projectForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
        
        function deleteProject(id) {
            if (confirm('Вы уверены, что хотите удалить этот проект?')) {
                fetch('save_project.php', {
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
        
        // Обновление предпросмотра
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('previewTitle').textContent = this.value || 'Название проекта';
        });
        
        document.getElementById('description').addEventListener('input', function() {
            const text = this.value || 'Описание проекта';
            document.getElementById('previewDescription').textContent = text.substring(0, 100) + (text.length > 100 ? '...' : '');
        });
        
        document.getElementById('image').addEventListener('input', function() {
            const img = document.getElementById('previewImage');
            if (this.value) {
                img.src = this.value;
                img.onerror = function() {
                    this.src = '../assets/img/placeholder.jpg';
                };
            } else {
                img.src = '../assets/img/placeholder.jpg';
            }
        });
    </script>
</body>
</html>