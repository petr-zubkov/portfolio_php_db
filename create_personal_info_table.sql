-- Файл: create_personal_info_table.sql
-- SQL-запросы для создания таблицы персональной информации

-- Таблица персональной информации
CREATE TABLE IF NOT EXISTS `personal_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `profession` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `telegram` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `experience_years` int(11) NOT NULL,
  `projects_count` int(11) NOT NULL,
  `clients_count` int(11) NOT NULL,
  `social_links` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставляем начальные данные
INSERT INTO `personal_info` (`id`, `full_name`, `profession`, `bio`, `avatar`, `email`, `phone`, `telegram`, `location`, `experience_years`, `projects_count`, `clients_count`, `social_links`) VALUES
(1, 'Пётр Зубков', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий с многолетним стажем работы. Специализируюсь на сложных многостраничных изданиях, учебниках и художественной литературе.', 'assets/img/placeholder.jpg', 'petr-zubkov@mail.ru', '+7 (999) 123-45-67', '@petr-zubkov', 'Москва, Россия', 5, 100, 50, '{"github":"","linkedin":"","twitter":"","website":""}');

-- Обновляем структуру таблицы themes, удаляя персональные поля
ALTER TABLE `themes` 
DROP COLUMN IF EXISTS `hero_title`,
DROP COLUMN IF EXISTS `hero_subtitle`,
DROP COLUMN IF EXISTS `avatar`,
DROP COLUMN IF EXISTS `about_text`,
DROP COLUMN IF EXISTS `experience_years`,
DROP COLUMN IF EXISTS `projects_count`,
DROP COLUMN IF EXISTS `clients_count`;

-- Обновляем структуру таблицы settings, удаляя персональные поля
ALTER TABLE `settings` 
DROP COLUMN IF EXISTS `hero_title`,
DROP COLUMN IF EXISTS `hero_subtitle`,
DROP COLUMN IF EXISTS `avatar`,
DROP COLUMN IF EXISTS `about_text`,
DROP COLUMN IF EXISTS `experience_years`,
DROP COLUMN IF EXISTS `projects_count`,
DROP COLUMN IF EXISTS `clients_count`;