// Сохранение настроек
document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Показываем индикатор загрузки
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
    
    const formData = new FormData(this);
    
    fetch('save_settings.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers]);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Проверяем Content-Type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error(`Ожидаемый JSON, получен ${contentType}`);
        }
        
        return response.text().then(text => {
            console.log('Response text:', text);
            
            // Проверяем, что ответ не начинается с HTML
            if (text.trim().startsWith('<')) {
                throw new Error(`Сервер вернул HTML вместо JSON: ${text.substring(0, 100)}...`);
            }
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                throw new Error(`Невалидный JSON: ${text.substring(0, 200)}...`);
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        
        if (data.success) {
            showNotification('Настройки успешно сохранены!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('Ошибка запроса: ' + error.message, 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});