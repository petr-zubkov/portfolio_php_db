// Переключение вкладок
document.querySelectorAll('.admin-sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Удаляем активный класс у всех ссылок и вкладок
        document.querySelectorAll('.admin-sidebar .nav-link').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        
        // Добавляем активный класс текущей ссылке
        this.classList.add('active');
        
        // Показываем соответствующую вкладку
        const tabId = this.getAttribute('data-tab') + '-tab';
        document.getElementById(tabId).classList.add('active');
    });
});

// Сохранение контактов
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('save_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Контакты успешно сохранены!');
        }
    });
});

// Сохранение настроек
document.getElementById('settingsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('save_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Настройки успешно сохранены!');
        }
    });
});

// Добавление проекта
document.getElementById('addProjectForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'add');
    
    fetch('save_project.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Проект успешно добавлен!');
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    });
});

// Редактирование проекта
document.querySelectorAll('.edit-project').forEach(btn => {
    btn.addEventListener('click', function() {
        const projectId = this.getAttribute('data-id');
        
        // Получаем данные проекта
        fetch(`get_project.php?id=${projectId}`)
        .then(response => response.json())
        .then(project => {
            if (project) {
                // Заполняем форму
                document.querySelector('#editProjectForm input[name="id"]').value = project.id;
                document.querySelector('#editProjectForm input[name="title"]').value = project.title;
                document.querySelector('#editProjectForm textarea[name="description"]').value = project.description;
                document.querySelector('#editProjectForm input[name="link"]').value = project.link;
                
                // Показываем модальное окно
                const modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
                modal.show();
            }
        });
    });
});

// Сохранение редактирования проекта
document.getElementById('editProjectForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'edit');
    
    fetch('save_project.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Проект успешно обновлен!');
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    });
});

// Удаление проекта
document.querySelectorAll('.delete-project').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Вы уверены, что хотите удалить этот проект?')) {
            const projectId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', projectId);
            
            fetch('save_project.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
});

// Добавление навыка
document.getElementById('addSkillForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'add');
    
    fetch('save_skills.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Навык успешно добавлен!');
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    });
});

// Редактирование навыка
document.querySelectorAll('.edit-skill').forEach(btn => {
    btn.addEventListener('click', function() {
        const skillId = this.getAttribute('data-id');
        
        // Получаем данные навыка
        fetch(`get_skill.php?id=${skillId}`)
        .then(response => response.json())
        .then(skill => {
            if (skill) {
                // Заполняем форму
                document.querySelector('#editSkillForm input[name="id"]').value = skill.id;
                document.querySelector('#editSkillForm input[name="name"]').value = skill.name;
                document.querySelector('#editSkillForm input[name="icon"]').value = skill.icon;
                document.querySelector('#editSkillForm input[name="level"]').value = skill.level;
                
                // Показываем модальное окно
                const modal = new bootstrap.Modal(document.getElementById('editSkillModal'));
                modal.show();
            }
        });
    });
});

// Сохранение редактирования навыка
document.getElementById('editSkillForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'edit');
    
    fetch('save_skills.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Навык успешно обновлен!');
            location.reload();
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    });
});

// Удаление навыка
document.querySelectorAll('.delete-skill').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Вы уверены, что хотите удалить этот навык?')) {
            const skillId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', skillId);
            
            fetch('save_skills.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
});