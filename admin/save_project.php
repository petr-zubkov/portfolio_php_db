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
    if (empty($title) || empty($description)) {
        echo json_encode([
            'success' => false,
            'message' => 'Заполните все обязательные поля'
        ]);
        exit;
    }
    
    // Обработка загрузки файла
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image_file']['type'], $allowed_types)) {
            echo json_encode([
                'success' => false,
                'message' => 'Допустимы только форматы: JPG, PNG, GIF, WebP'
            ]);
            exit;
        }
        
        if ($_FILES['image_file']['size'] > $max_size) {
            echo json_encode([
                'success' => false,
                'message' => 'Максимальный размер файла: 5MB'
            ]);
            exit;
        }
        
        // Создаем папку uploads, если она не существует
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Генерируем уникальное имя файла
        $file_extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
            $image = 'uploads/' . $file_name;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при загрузке файла'
            ]);
            exit;
        }
    }
    
    // Если нет ни загруженного файла, ни URL изображения
    if (empty($image)) {
        echo json_encode([
            'success' => false,
            'message' => 'Загрузите изображение или укажите URL'
        ]);
        exit;
    }
    
    // Сохранение в базу данных
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
            'message' => $message,
            'image_path' => $image
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при сохранении проекта: ' . $conn->error
        ]);
    }
    exit;
}

// Удаление проекта
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Получаем информацию о проекте для удаления файла
    $stmt = $conn->prepare("SELECT image FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    
    // Удаляем файл изображения
    if ($project && !empty($project['image']) && file_exists('../' . $project['image'])) {
        unlink('../' . $project['image']);
    }
    
    // Удаляем проект из базы данных
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Проект успешно удален'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при удалении проекта'
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
    $edit_id = (int)$_GET['edit'];
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
    <style>
        .image-upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .image-upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .image-upload-area.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .preview-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .preview-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        .preview-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #6c757d;
            background: #f8f9fa;
        }
        .image-info {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .url-input-group {
            position: relative;
        }
        .url-input-group .clear-url {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }
        .url-input-group .clear-url:hover {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4"><?php echo $edit_project ? 'Редактировать проект' : 'Добавить проект'; ?></h2>
            
            <form id="projectForm" class="admin-form" enctype="multipart/form-data">
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
                        
                        <div class="mb-3">
                            <label class="form-label">Изображение проекта <span class="text-danger">*</span></label>
                            
                            <!-- Загрузка файла -->
                            <div class="image-upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                <h6>Перетащите изображение сюда или нажмите для выбора</h6>
                                <p class="text-muted small">Допустимые форматы: JPG, PNG, GIF, WebP (макс. 5MB)</p>
                                <input type="file" class="d-none" id="image_file" name="image_file" accept="image/*">
                            </div>
                            
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="use_url">
                                    <label class="form-check-label" for="use_url">
                                        Использовать URL изображения вместо загрузки
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Поле для URL -->
                            <div class="url-input-group mt-3" id="urlGroup" style="display: none;">
                                <input type="url" class="form-control" id="image" name="image" 
                                       placeholder="https://example.com/image.jpg"
                                       value="<?php echo htmlspecialchars($edit_project['image'] ?? ''); ?>">
                                <button type="button" class="clear-url" id="clearUrl">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="image-info">
                                    Укажите полный URL изображения, если вы не хотите загружать файл
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link" class="form-label">Ссылка на проект</label>
                            <input type="url" class="form-control" id="link" name="link" 
                                   placeholder="https://example.com"
                                   value="<?php echo htmlspecialchars($edit_project['link'] ?? '#'); ?>">
                            <div class="form-text">Оставьте # если нет внешней ссылки</div>
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
                                <div class="preview-container">
                                    <?php if ($edit_project && !empty($edit_project['image'])): ?>
                                        <?php if (strpos($edit_project['image'], 'uploads/') === 0): ?>
                                            <img id="previewImage" src="../<?php echo htmlspecialchars($edit_project['image']); ?>" 
                                                 alt="Предпросмотр" class="preview-image"
                                                 onerror="this.style.display='none'; document.getElementById('previewPlaceholder').style.display='flex';">
                                        <?php else: ?>
                                            <img id="previewImage" src="<?php echo htmlspecialchars($edit_project['image']); ?>" 
                                                 alt="Предпросмотр" class="preview-image"
                                                 onerror="this.style.display='none'; document.getElementById('previewPlaceholder').style.display='flex';">
                                        <?php endif; ?>
                                        <div id="previewPlaceholder" class="preview-placeholder" style="display: none;">
                                            <div class="text-center">
                                                <i class="fas fa-image fa-3x mb-2"></i>
                                                <p>Изображение не доступно</p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div id="previewPlaceholder" class="preview-placeholder">
                                            <div class="text-center">
                                                <i class="fas fa-image fa-3x mb-2"></i>
                                                <p>Загрузите изображение</p>
                                            </div>
                                        </div>
                                        <img id="previewImage" src="" alt="Предпросмотр" class="preview-image" style="display: none;"
                                             onerror="this.style.display='none'; document.getElementById('previewPlaceholder').style.display='flex';">
                                    <?php endif; ?>
                                </div>
                                <h6 id="previewTitle"><?php echo htmlspecialchars($edit_project['title'] ?? 'Название проекта'); ?></h6>
                                <p class="small text-muted" id="previewDescription"><?php echo htmlspecialchars(substr($edit_project['description'] ?? 'Описание проекта', 0, 100)) . '...'; ?></p>
                                <?php if ($edit_project && !empty($edit_project['image'])): ?>
                                    <div class="image-info">
                                        <small>Текущее изображение: <?php echo htmlspecialchars($edit_project['image']); ?></small>
                                    </div>
                                <?php endif; ?>
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
                                        <th>Изображение</th>
                                        <th>Название</th>
                                        <th>Описание</th>
                                        <th>Дата создания</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($project['image'])): ?>
                                                    <?php if (strpos($project['image'], 'uploads/') === 0): ?>
                                                        <img src="../<?php echo htmlspecialchars($project['image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                             onerror="this.src='../assets/img/placeholder.jpg'">
                                                    <?php else: ?>
                                                        <img src="<?php echo htmlspecialchars($project['image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                             onerror="this.src='../assets/img/placeholder.jpg'">
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <img src="../assets/img/placeholder.jpg" 
                                                         alt="Нет изображения" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                <?php endif; ?>
                                            </td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('image_file');
            const useUrlCheckbox = document.getElementById('use_url');
            const urlGroup = document.getElementById('urlGroup');
            const urlInput = document.getElementById('image');
            const clearUrlBtn = document.getElementById('clearUrl');
            const previewImage = document.getElementById('previewImage');
            const previewPlaceholder = document.getElementById('previewPlaceholder');
            
            // Обработка клика по области загрузки
            uploadArea.addEventListener('click', function() {
                if (!useUrlCheckbox.checked) {
                    fileInput.click();
                }
            });
            
            // Переключение между загрузкой файла и URL
            useUrlCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    urlGroup.style.display = 'block';
                    uploadArea.style.opacity = '0.5';
                    uploadArea.style.pointerEvents = 'none';
                } else {
                    urlGroup.style.display = 'none';
                    uploadArea.style.opacity = '1';
                    uploadArea.style.pointerEvents = 'auto';
                    urlInput.value = '';
                }
            });
            
            // Очистка URL
            clearUrlBtn.addEventListener('click', function() {
                urlInput.value = '';
                updatePreview();
            });
            
            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (!useUrlCheckbox.checked) {
                    uploadArea.classList.add('dragover');
                }
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                if (!useUrlCheckbox.checked) {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        handleFileSelect(files[0]);
                    }
                }
            });
            
            // Обработка выбора файла
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });
            
            // Обработка ввода URL
            urlInput.addEventListener('input', updatePreview);
            
            function handleFileSelect(file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewPlaceholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            }
            
            function updatePreview() {
                const url = urlInput.value.trim();
                if (url) {
                    previewImage.src = url;
                    previewImage.style.display = 'block';
                    previewPlaceholder.style.display = 'none';
                } else {
                    previewImage.style.display = 'none';
                    previewPlaceholder.style.display = 'flex';
                }
            }
            
            // Обновление предпросмотра при изменении текстовых полей
            document.getElementById('title').addEventListener('input', function() {
                document.getElementById('previewTitle').textContent = this.value || 'Название проекта';
            });
            
            document.getElementById('description').addEventListener('input', function() {
                const text = this.value || 'Описание проекта';
                document.getElementById('previewDescription').textContent = text.substring(0, 100) + (text.length > 100 ? '...' : '');
            });
            
            // Обработка отправки формы
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
                    showAlert('danger', 'Произошла ошибка при отправке запроса');
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
        });
        
        function deleteProject(id) {
            if (confirm('Вы уверены, что хотите удалить этот проект?')) {
                fetch('save_project.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'id': id,
                        'delete': 'true'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    showAlert('danger', 'Произошла ошибка');
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>