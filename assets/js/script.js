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

// 🎯 ОСНОВНОЙ ОБРАБОТЧИК ФОРМЫ КОНТАКТОВ - АСИНХРОННАЯ ОТПРАВКА
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault(); // Предотвращаем стандартную отправку формы
    
    const form = this;
    const submitButton = document.getElementById('submitBtn');
    const btnText = submitButton.querySelector('.btn-text');
    const btnSpinner = submitButton.querySelector('.btn-spinner');
    const messageContainer = document.getElementById('formMessage');
    
    // Получаем данные из формы
    const formData = new FormData(form);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        message: formData.get('message')
    };
    
    // Валидация данных
    if (!data.name.trim()) {
        showFormMessage('Пожалуйста, введите ваше имя', 'error');
        return;
    }
    
    if (!data.email.trim() || !isValidEmail(data.email)) {
        showFormMessage('Пожалуйста, введите корректный email', 'error');
        return;
    }
    
    if (!data.message.trim()) {
        showFormMessage('Пожалуйста, введите ваше сообщение', 'error');
        return;
    }
    
    // Показываем индикатор загрузки
    setLoadingState(true);
    
    // 📧 ОТПРАВЛЯЕМ ДАННЫЕ НА СЕРВЕР АСИНХРОННО
    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка сети');
        }
        return response.text(); // Получаем текст ответа
    })
    .then(text => {
        // Проверяем, является ли ответ JSON
        try {
            const result = JSON.parse(text);
            if (result.success) {
                showFormMessage(result.message || 'Ваше сообщение успешно отправлено!', 'success');
                form.reset(); // Очищаем форму
            } else {
                showFormMessage(result.message || 'Произошла ошибка при отправке.', 'error');
            }
        } catch (e) {
            // Если ответ не JSON, проверяем на наличие сообщений об успехе/ошибке
            if (text.includes('успешно') || text.includes('отправлено')) {
                showFormMessage('Ваше сообщение успешно отправлено!', 'success');
                form.reset();
            } else {
                showFormMessage('Произошла ошибка при отправке сообщения.', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFormMessage('Произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже.', 'error');
    })
    .finally(() => {
        // Всегда восстанавливаем кнопку
        setLoadingState(false);
    });
});

// Функция для установки состояния загрузки
function setLoadingState(loading) {
    const submitButton = document.getElementById('submitBtn');
    const btnText = submitButton.querySelector('.btn-text');
    const btnSpinner = submitButton.querySelector('.btn-spinner');
    
    if (loading) {
        submitButton.disabled = true;
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');
        submitButton.style.cursor = 'not-allowed';
        submitButton.style.opacity = '0.7';
    } else {
        submitButton.disabled = false;
        btnText.classList.remove('d-none');
        btnSpinner.classList.add('d-none');
        submitButton.style.cursor = 'pointer';
        submitButton.style.opacity = '1';
    }
}

// Функция для показа сообщений в форме
function showFormMessage(message, type) {
    const messageContainer = document.getElementById('formMessage');
    
    // Очищаем предыдущие сообщения
    messageContainer.innerHTML = '';
    
    // Создаем элемент для сообщения
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    messageDiv.style.marginTop = '20px';
    messageDiv.style.borderRadius = '8px';
    messageDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    
    // Добавляем иконку и кнопку закрытия
    if (type === 'success') {
        messageDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>${type === 'success' ? 'Успешно!' : 'Ошибка!'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        `;
        messageDiv.style.borderLeft = '4px solid #28a745';
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.borderColor = '#c3e6cb';
        messageDiv.style.color = '#155724';
    } else {
        messageDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Ошибка!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        `;
        messageDiv.style.borderLeft = '4px solid #dc3545';
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.borderColor = '#f5c6cb';
        messageDiv.style.color = '#721c24';
    }
    
    // Добавляем сообщение в контейнер
    messageContainer.appendChild(messageDiv);
    
    // Плавная анимация появления
    messageDiv.style.opacity = '0';
    messageDiv.style.transform = 'translateY(-10px)';
    messageDiv.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateY(0)';
    }, 10);
    
    // Прокручиваем к сообщению
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Автоматически скрываем сообщение через 7 секунд для успешных сообщений
    if (type === 'success') {
        setTimeout(() => {
            if (messageDiv.parentNode) {
                const bsAlert = new bootstrap.Alert(messageDiv);
                bsAlert.close();
            }
        }, 7000);
    }
}

// Функция для валидации email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
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

// Обработка закрытия alert сообщений
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-close')) {
        const alert = e.target.closest('.alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }
    }
});

// 📋 ИНСТРУКЦИЯ:
// Этот файл настроен для асинхронной отправки формы
// 
// Что он делает:
// 1. Предотвращает стандартную отправку формы (без перезагрузки страницы)
// 2. Собирает и валидирует данные формы
// 3. Показывает визуальный индикатор загрузки
// 4. Отправляет данные асинхронно через fetch API
// 5. Показывает красивые сообщения об успехе/ошибке
// 6. Очищает форму при успешной отправке
//
// Преимущества:
// - Нет перезагрузки страницы (исправляет "дерганье")
// - Визуальная обратная связь для пользователя
// - Красивые уведомления вместо alert
// - Обработка ошибок и сетевых проблем