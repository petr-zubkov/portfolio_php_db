<?php
session_start();
require_once '../config.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: auth.php');
    exit;
}

// Обработка формы смены пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Валидация
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Все поля обязательны для заполнения'
        ]);
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        echo json_encode([
            'success' => false,
            'message' => 'Новые пароли не совпадают'
        ]);
        exit;
    }
    
    if (strlen($new_password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Пароль должен содержать минимум 6 символов'
        ]);
        exit;
    }
    
    // Проверка текущего пароля (в реальном проекте здесь должна быть более безопасная проверка)
    if ($current_password === 'admin123') {
        // В реальном проекте здесь нужно обновлять пароль в базе данных
        // Для демонстрации используем файловую систему
        $password_file = '../admin_password.txt';
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        try {
            file_put_contents($password_file, $hashed_password);
            echo json_encode([
                'success' => true,
                'message' => 'Пароль успешно изменен'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при сохранении пароля'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Текущий пароль неверен'
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
    <title>Смена пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
        .password-requirements {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .requirement {
            margin-bottom: 0.25rem;
        }
        .requirement.met {
            color: #28a745;
        }
        .requirement.not-met {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="mb-4">Смена пароля</h2>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Здесь вы можете изменить пароль для доступа к админ-панели. Пожалуйста, используйте надежный пароль.
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Внимание!</strong> После смены пароля вам потребуется войти заново.
                    </div>
                    
                    <form id="passwordForm" class="admin-form">
                        <div class="mb-4">
                            <label for="current_password" class="form-label">Текущий пароль <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="new_password" class="form-label">Новый пароль <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <div class="password-requirements">
                                <div class="requirement" id="lengthReq">
                                    <i class="fas fa-times"></i> Минимум 6 символов
                                </div>
                                <div class="requirement" id="uppercaseReq">
                                    <i class="fas fa-times"></i> Хотя бы одна заглавная буква
                                </div>
                                <div class="requirement" id="numberReq">
                                    <i class="fas fa-times"></i> Хотя бы одна цифра
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Подтверждение нового пароля <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="form-text"></div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                <i class="fas fa-save"></i> Изменить пароль
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Отмена
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Безопасность пароля</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3">Рекомендации по созданию надежного пароля:</h6>
                            <ul class="small">
                                <li>Используйте минимум 8 символов</li>
                                <li>Комбинируйте заглавные и строчные буквы</li>
                                <li>Добавляйте цифры и специальные символы</li>
                                <li>Не используйте личную информацию</li>
                                <li>Не используйте одинаковые пароли на разных сайтах</li>
                            </ul>
                            
                            <hr>
                            
                            <h6 class="mb-3">Примеры надежных паролей:</h6>
                            <div class="small">
                                <code>MySecureP@ssw0rd!</code><br>
                                <code>S3cur3P@ss2024</code><br>
                                <code>Adm!nP@ssw0rd123</code>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-history"></i> История изменений</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-0">
                                Информация о последних изменениях пароля недоступна в демонстрационной версии.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrength');
            const lengthReq = document.getElementById('lengthReq');
            const uppercaseReq = document.getElementById('uppercaseReq');
            const numberReq = document.getElementById('numberReq');
            
            let strength = 0;
            
            // Проверка длины
            if (password.length >= 6) {
                strength++;
                lengthReq.className = 'requirement met';
                lengthReq.innerHTML = '<i class="fas fa-check"></i> Минимум 6 символов';
            } else {
                lengthReq.className = 'requirement not-met';
                lengthReq.innerHTML = '<i class="fas fa-times"></i> Минимум 6 символов';
            }
            
            // Проверка заглавных букв
            if (/[A-Z]/.test(password)) {
                strength++;
                uppercaseReq.className = 'requirement met';
                uppercaseReq.innerHTML = '<i class="fas fa-check"></i> Хотя бы одна заглавная буква';
            } else {
                uppercaseReq.className = 'requirement not-met';
                uppercaseReq.innerHTML = '<i class="fas fa-times"></i> Хотя бы одна заглавная буква';
            }
            
            // Проверка цифр
            if (/[0-9]/.test(password)) {
                strength++;
                numberReq.className = 'requirement met';
                numberReq.innerHTML = '<i class="fas fa-check"></i> Хотя бы одна цифра';
            } else {
                numberReq.className = 'requirement not-met';
                numberReq.innerHTML = '<i class="fas fa-times"></i> Хотя бы одна цифра';
            }
            
            // Обновление индикатора надежности
            strengthBar.className = 'password-strength';
            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength === 2) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }
        
        function checkPasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (newPassword === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Пароли совпадают</span>';
            } else {
                matchDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Пароли не совпадают</span>';
            }
        }
        
        document.getElementById('new_password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
        
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const changeBtn = document.getElementById('changePasswordBtn');
            const originalText = changeBtn.innerHTML;
            
            changeBtn.disabled = true;
            changeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Изменение пароля...';
            
            const formData = new FormData(this);
            
            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = 'logout.php';
                    }, 2000);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Произошла ошибка');
                console.error('Error:', error);
            })
            .finally(() => {
                changeBtn.disabled = false;
                changeBtn.innerHTML = originalText;
            });
        });
        
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('passwordForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
    </script>
</body>
</html>