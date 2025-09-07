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
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $telegram = $_POST['telegram'] ?? '';
    
    // Валидация
    if (empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email обязателен'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Введите корректный email'
        ]);
        exit;
    }
    
    // Обновление контактов (всегда обновляем первую запись)
    $stmt = $conn->prepare("UPDATE contact SET email=?, phone=?, telegram=? WHERE id = 1");
    $stmt->bind_param("sss", $email, $phone, $telegram);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Контактная информация успешно обновлена'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка при обновлении контактов'
        ]);
    }
    exit;
}

// Получаем текущие контакты
$contact_result = $conn->query("SELECT * FROM contact LIMIT 1");
$contact = $contact_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление контактами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <h2 class="mb-4">Управление контактами</h2>
            
            <form id="contactForm" class="admin-form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($contact['phone'] ?? ''); ?>"
                                               placeholder="+7 (999) 123-45-67">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telegram" class="form-label">Telegram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-telegram"></i></span>
                                <input type="text" class="form-control" id="telegram" name="telegram" 
                                       value="<?php echo htmlspecialchars($contact['telegram'] ?? ''); ?>"
                                       placeholder="@username">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> Сохранить контакты
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
                                <div class="contact-item">
                                    <i class="fas fa-envelope fa-2x"></i>
                                    <div>
                                        <h6>Email</h6>
                                        <p id="previewEmail"><?php echo htmlspecialchars($contact['email'] ?? 'your.email@example.com'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="contact-item mt-3">
                                    <i class="fas fa-phone fa-2x"></i>
                                    <div>
                                        <h6>Телефон</h6>
                                        <p id="previewPhone"><?php echo htmlspecialchars($contact['phone'] ?? '+7 (999) 123-45-67'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="contact-item mt-3">
                                    <i class="fab fa-telegram fa-2x"></i>
                                    <div>
                                        <h6>Telegram</h6>
                                        <p id="previewTelegram"><?php echo htmlspecialchars($contact['telegram'] ?? '@username'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Информация</h6>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <p><strong>Email:</strong> Обязательное поле, будет отображаться на сайте</p>
                                    <p><strong>Телефон:</strong> Необязательное поле</p>
                                    <p><strong>Telegram:</strong> Необязательное поле, укажите с @</p>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
            
            const formData = new FormData(this);
            
            fetch('save_contact.php', {
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
            
            const form = document.getElementById('contactForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
        
        // Обновление предпросмотра
        document.getElementById('email').addEventListener('input', function() {
            document.getElementById('previewEmail').textContent = this.value || 'your.email@example.com';
        });
        
        document.getElementById('phone').addEventListener('input', function() {
            document.getElementById('previewPhone').textContent = this.value || '+7 (999) 123-45-67';
        });
        
        document.getElementById('telegram').addEventListener('input', function() {
            document.getElementById('previewTelegram').textContent = this.value || '@username';
        });
    </script>
</body>
</html>