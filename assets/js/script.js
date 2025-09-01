// Плавная прокрутка
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

// Изменение навигации при прокрутке
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

// Форма контактов
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    
    // Получаем данные формы
    const formData = new FormData(form);
    const data = {
        name: formData.get('name') || form.querySelector('input[placeholder="Ваше имя"]').value,
        email: formData.get('email') || form.querySelector('input[placeholder="Ваш email"]').value,
        message: formData.get('message') || form.querySelector('textarea[placeholder="Ваше сообщение"]').value
    };
    
    // Валидация
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
    
    // Отправляем данные на сервер
    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showMessage(result.message, 'success');
            form.reset();
        } else {
            showMessage(result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже.', 'error');
    })
    .finally(() => {
        // Восстанавливаем кнопку
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
    });
});

// Функция для валидации email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Функция для показа сообщений
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
    alertDiv.textContent = message;
    
    // Добавляем сообщение после формы
    const form = document.getElementById('contactForm');
    form.parentNode.insertBefore(alertDiv, form.nextSibling);
    
    // Автоматически скрываем сообщение через 5 секунд
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Анимация при прокрутке
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

// Наблюдаем за элементами
document.querySelectorAll('.skill-card, .portfolio-card, .stat-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});