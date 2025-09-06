// Плавная прокрутка для навигационных ссылок
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Изменение навигации при прокрутке страницы
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.padding = '0.5rem 0';
        navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    } else {
        navbar.style.padding = '1rem 0';
        navbar.style.boxShadow = 'none';
    }
});

// 🎯 ОСНОВНОЙ ОБРАБОТЧИК ФОРМЫ КОНТАКТОВ - ИСПРАВЛЕННАЯ ВЕРСИЯ
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    
    // Получаем данные из формы
    const formData = new FormData(form);
    const data = {
        name: formData.get('name') || form.querySelector('input[placeholder="Ваше имя"]').value,
        email: formData.get('email') || form.querySelector('input[placeholder="Ваш email"]').value,
        message: formData.get('message') || form.querySelector('textarea[placeholder="Ваше сообщение"]').value
    };
    
    // Валидация данных
    if (!data.name.trim()) {
        showMessage('Пожалуйста, введите ваше имя', 'error');
        return;
    }
    
    if (!data.email.trim() || !isValidEmail(data.email)) {
        showMessage('Пожалуйста, введите корректный email', 'error');
        return;
    }
    
    if (!data.message.trim()) {
        showMessage('Пожалуйста, введите ваше сообщение', 'error');
        return;
    }
    
    // Показываем индикатор загрузки
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Отправка...';
    
    // 📧 ОТПРАВЛЯЕМ ДАННЫЕ НА СЕРВЕР - ИСПРАВЛЕНО!
    // Используем финальный SMTP обработчик
    fetch('send_message_fixed_smtp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(response => {
        // Проверяем, что ответ в формате JSON
        if (!response.ok) {
            throw new Error('Ошибка сети');
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            // ✅ Успешная отправка
            showMessage(result.message, 'success');
            form.reset(); // Очищаем форму
            
            // Дополнительная информация для отладки
            if (result.debug) {
                console.log('Debug info:', result.debug);
            }
        } else {
            // ❌ Ошибка при отправке
            showMessage(result.message, 'error');
            
            // Показываем дополнительную информацию для отладки
            if (result.debug) {
                console.error('Debug info:', result.debug);
            }
        }
    })
    .catch(error => {
        // ❌ Ошибка сети или сервера
        console.error('Error:', error);
        showMessage('Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже.', 'error');
    })
    .finally(() => {
        // Всегда восстанавливаем кнопку
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
    });
});

// Функция для валидации email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Функция для показа сообщений пользователю
function showMessage(message, type) {
    // Удаляем существующие сообщения
    const existingAlert = document.querySelector('.form-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Создаем элемент для сообщения
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} form-alert`;
    alertDiv.style.marginTop = '15px';
    alertDiv.style.padding = '12px 20px';
    alertDiv.style.borderRadius = '8px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    alertDiv.textContent = message;
    
    // Добавляем иконку в зависимости от типа
    if (type === 'success') {
        alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + message;
    } else {
        alertDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + message;
    }
    
    // Добавляем сообщение после формы
    const form = document.getElementById('contactForm');
    form.parentNode.insertBefore(alertDiv, form.nextSibling);
    
    // Плавная анимация появления
    alertDiv.style.opacity = '0';
    alertDiv.style.transform = 'translateY(-10px)';
    alertDiv.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
        alertDiv.style.opacity = '1';
        alertDiv.style.transform = 'translateY(0)';
    }, 10);
    
    // Автоматически скрываем сообщение через 5 секунд
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 300);
    }, 5000);
}

// Анимация элементов при прокрутке страницы
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Применяем анимацию к карточкам навыков, портфолио и статистики
document.querySelectorAll('.skill-card, .portfolio-card, .stat-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// 📋 ИНСТРУКЦИЯ:
// Этот файл настроен для работы с SMTP обработчиком
// 
// Что он делает:
// 1. Собирает данные из формы контактов
// 2. Проверяет их корректность
// 3. Показывает индикатор загрузки
// 4. Отправляет данные на send_message_smtp_final.php (ИСПРАВЛЕНО!)
// 5. Показывает результат пользователю
// 6. Сохраняет отладочную информацию в консоль
//
// Для работы нужно:
// - Настроить пароль в config.php
// - Включить SMTP в настройках Mail.ru
// - Установить PHPMailer (install_phpmailer_working.php)
// - Протестировать через test_smtp_working.php