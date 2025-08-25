-- Проверяем существование таблицы
SHOW TABLES LIKE 'settings';

-- Если таблицы нет, создаем ее
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_title` varchar(255) NOT NULL,
  `hero_title` varchar(255) NOT NULL,
  `hero_subtitle` text NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `about_text` text NOT NULL,
  `primary_color` varchar(7) NOT NULL,
  `secondary_color` varchar(7) NOT NULL,
  `accent_color` varchar(7) NOT NULL,
  `text_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `font_family` varchar(50) NOT NULL,
  `bg_image` varchar(255) NOT NULL,
  `experience_years` int(11) NOT NULL,
  `projects_count` int(11) NOT NULL,
  `clients_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Проверяем, есть ли данные
SELECT * FROM settings;

-- Если данных нет, вставляем начальные значения
INSERT INTO `settings` (`id`, `site_title`, `hero_title`, `hero_subtitle`, `avatar`, `about_text`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color`, `font_family`, `bg_image`, `experience_years`, `projects_count`, `clients_count`) VALUES
(1, 'Портфолио верстальщика книг', 'Верстальщик книг', 'Профессиональная верстка печатных и электронных изданий', 'assets/img/placeholder.jpg', '', '#2c3e50', '#3498db', '#e74c3c', '#333333', '#ffffff', 'Roboto', '', 5, 100, 50);