-- Файл: create_themes_table.sql
-- SQL-запросы для создания таблицы тем

-- Таблица тем
CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `primary_color` varchar(7) NOT NULL,
  `secondary_color` varchar(7) NOT NULL,
  `accent_color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `font_family` varchar(50) NOT NULL,
  `bg_image` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставляем начальные темы
INSERT INTO `themes` (`id`, `name`, `slug`, `description`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color`, `font_family`, `bg_image`, `is_active`) VALUES
(1, 'Космос', 'space', 'Космическая тема с глубокими синими и фиолетовыми оттенками', '#0f0c29', '#302b63', '#24243e', '#e0e0e0', '#1a1a2e', 'Orbitron', '', 1),
(2, 'Вода', 'water', 'Водная тема с голубыми и бирюзовыми оттенками', '#006ba6', '#0496ff', '#3da9fc', '#333333', '#f0f8ff', 'Montserrat', '', 0),
(3, 'Лес', 'forest', 'Лесная тема с зелеными и природными оттенками', '#1e3a1e', '#2d5a2d', '#4a7c59', '#333333', '#f5f5dc', 'Roboto', '', 0);