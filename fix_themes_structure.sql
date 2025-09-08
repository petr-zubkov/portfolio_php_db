-- Файл: fix_themes_structure.sql
-- Исправление структуры таблицы themes - удаление личной информации

-- Шаг 1: Удаляем колонки с личной информацией из таблицы themes (если они существуют)
ALTER TABLE themes DROP COLUMN IF EXISTS site_title;
ALTER TABLE themes DROP COLUMN IF EXISTS hero_title;
ALTER TABLE themes DROP COLUMN IF EXISTS hero_subtitle;
ALTER TABLE themes DROP COLUMN IF EXISTS avatar;
ALTER TABLE themes DROP COLUMN IF EXISTS about_text;
ALTER TABLE themes DROP COLUMN IF EXISTS experience_years;
ALTER TABLE themes DROP COLUMN IF EXISTS projects_count;
ALTER TABLE themes DROP COLUMN IF EXISTS clients_count;

-- Шаг 2: Обновляем данные в таблице settings, чтобы они содержали актуальную личную информацию
-- Сначала проверяем, есть ли данные в settings
SELECT * FROM settings LIMIT 1;

-- Если данных нет, вставляем базовые настройки
INSERT IGNORE INTO settings (id, site_title, hero_title, hero_subtitle, avatar, about_text, primary_color, secondary_color, accent_color, text_color, bg_color, font_family, bg_image, experience_years, projects_count, clients_count) 
VALUES (1, 'Портфолио верстальщика книг', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий', 'assets/img/placeholder.jpg', 'Опытный верстальщик с многолетним стажем работы в области книжной верстки.', '#2c3e50', '#3498db', '#e74c3c', '#333333', '#ffffff', 'Roboto', '', 5, 100, 50);

-- Шаг 3: Проверяем, что в таблице themes только оформление
SELECT * FROM themes;

-- Шаг 4: Обновляем все темы, чтобы у них были правильные данные для оформления
UPDATE themes SET 
    primary_color = '#0f0c29',
    secondary_color = '#302b63', 
    accent_color = '#24243e',
    text_color = '#e0e0e0',
    bg_color = '#1a1a2e',
    font_family = 'Orbitron'
WHERE name = 'Космос';

UPDATE themes SET 
    primary_color = '#006ba6',
    secondary_color = '#0496ff', 
    accent_color = '#3da9fc',
    text_color = '#333333',
    bg_color = '#f0f8ff',
    font_family = 'Montserrat'
WHERE name = 'Вода';

UPDATE themes SET 
    primary_color = '#1e3a1e',
    secondary_color = '#2d5a2d', 
    accent_color = '#4a7c59',
    text_color = '#333333',
    bg_color = '#f5f5dc',
    font_family = 'Roboto'
WHERE name = 'Лес';