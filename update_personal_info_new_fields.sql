-- Файл: update_personal_info_new_fields.sql
-- SQL-запросы для добавления новых полей в таблицу персональной информации

-- Добавляем новые поля для хобби, любимых фильмов, книг и сайтов
ALTER TABLE `personal_info` 
ADD COLUMN `hobbies` text NOT NULL AFTER `social_links`,
ADD COLUMN `favorite_movies` text NOT NULL AFTER `hobbies`,
ADD COLUMN `my_books` text NOT NULL AFTER `favorite_movies`,
ADD COLUMN `websites` text NOT NULL AFTER `my_books`;

-- Обновляем существующую запись с примерами данных
UPDATE `personal_info` SET 
`hobbies` = '["Чтение научной фантастики", "Программирование", "Фотография", "Путешествия", "Астрономия"]',
`favorite_movies` = '["Интерстеллар", "Матрица", "Начало", "Марсианин", "Гравитация"]',
`my_books` = '["Космос Карла Сагана", "1984 Джорджа Оруэлла", "Дюна Фрэнка Герберта", "Мастер и Маргарита", "Three Body Problem"]',
`websites` = '[{"name":"GitHub","url":"https://github.com/petr-zubkov"},{"name":"LinkedIn","url":"https://linkedin.com/in/petr-zubkov"},{"name":"Блог","url":"https://blog.zubkov.space"}]'
WHERE `id` = 1;