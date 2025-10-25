-- ============================================
-- SONGLY - Full Database Setup Script
-- Создание таблиц в существующей базе данных
-- ============================================

-- Использование существующей базы данных
USE j27119254_song;

-- ============================================
-- 1. Таблица исполнителей (singer)
-- ============================================
CREATE TABLE IF NOT EXISTS `singer` (
  `id_singer` INT(11) NOT NULL AUTO_INCREMENT,
  `name_singer` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `followers` INT(11) DEFAULT 0,
  `contributions` INT(11) DEFAULT 0,
  `image_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_singer`),
  INDEX `idx_name_singer` (`name_singer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. Таблица песен (songs)
-- ============================================
CREATE TABLE IF NOT EXISTS `songs` (
  `id_songs` INT(11) NOT NULL AUTO_INCREMENT,
  `name_songs` VARCHAR(255) NOT NULL,
  `id_singer` INT(11) NOT NULL,
  `number_of_auditions` INT(11) DEFAULT 0,
  `image_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_songs`),
  INDEX `idx_singer` (`id_singer`),
  INDEX `idx_name_songs` (`name_songs`),
  FOREIGN KEY (`id_singer`) REFERENCES `singer`(`id_singer`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. Таблица альбомов (albums)
-- ============================================
CREATE TABLE IF NOT EXISTS `albums` (
  `id_albums` INT(11) NOT NULL AUTO_INCREMENT,
  `name_albums` VARCHAR(255) NOT NULL,
  `id_singer` INT(11) NOT NULL,
  `year_of_release` INT(4) DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_albums`),
  INDEX `idx_singer` (`id_singer`),
  INDEX `idx_year` (`year_of_release`),
  FOREIGN KEY (`id_singer`) REFERENCES `singer`(`id_singer`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. Таблица текстов песен (textsong)
-- ============================================
CREATE TABLE IF NOT EXISTS `textsong` (
  `id_songs` INT(11) NOT NULL,
  `producer` VARCHAR(255) DEFAULT NULL,
  `textsong` TEXT NOT NULL,
  PRIMARY KEY (`id_songs`),
  FOREIGN KEY (`id_songs`) REFERENCES `songs`(`id_songs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. Таблица администраторов (admin_users)
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- НАЧАЛЬНЫЕ ДАННЫЕ
-- ============================================

-- Создание администратора по умолчанию
-- Логин: admin
-- Пароль: admin123
-- ВАЖНО: Смените пароль после первого входа!
INSERT INTO `admin_users` (`username`, `password_hash`)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `username`='admin';

-- ============================================
-- ПРИМЕРЫ ДАННЫХ ДЛЯ ДЕМОНСТРАЦИИ
-- (Можно удалить если не нужны)
-- ============================================

-- Добавление исполнителей
INSERT INTO `singer` (`name_singer`, `description`, `followers`, `contributions`, `image_path`) VALUES
('Kendrick Lamar', 'American rapper, songwriter, and record producer. Widely regarded as one of the most influential rappers of his generation.', 14200000, 150, 'images/singer/1.png'),
('PARTYNEXTDOOR', 'Canadian R&B singer, songwriter, and record producer. Known for his unique sound and collaborations with Drake.', 1593000, 85, 'images/singer/2.png'),
('Lady Gaga', 'American singer, songwriter, and actress. Known for her image reinventions and musical versatility.', 8213000, 320, 'images/singer/3.png');

-- Добавление песен
INSERT INTO `songs` (`name_songs`, `id_singer`, `number_of_auditions`, `image_path`) VALUES
('Not Like Us', 1, 14200000, 'images/song/1.png'),
('MEET YOUR PADRE', 2, 159300, 'images/song/13.png'),
('Abracadabra', 3, 821300, 'images/song/10.png');

-- Добавление альбомов
INSERT INTO `albums` (`name_albums`, `id_singer`, `year_of_release`, `image_path`) VALUES
('Mr. Morale & The Big Steppers', 1, 2022, 'images/album/1.png'),
('PARTYNEXTDOOR TWO', 2, 2014, 'images/album/2.png'),
('Chromatica', 3, 2020, 'images/album/3.png');

-- Добавление текстов песен
INSERT INTO `textsong` (`id_songs`, `producer`, `textsong`) VALUES
(1, 'DJ Mustard', 'I see dead people\nI see dead people\nI see dead people\nI see dead people\n\nAyy\nDeebo, any rap [?] with a Ku Klux Klan\nI don''t feel like they''re mini, I''m a petty man\nI wanna kill ''em all, but I''m not ready yet\n\nThey not like us, they not like us, they not like us\nThey not like us, they not like us, they not like us'),
(2, 'PARTYNEXTDOOR', 'Yeah, yeah\nMeet your padre\nMeet your padre\nMeet your padre now\n\nI''m the one that you''ve been looking for\nI''m the one you need\nMeet your padre\nMeet your padre now'),
(3, 'BloodPop', 'Abracadabra\nI wanna reach out and grab ya\nAbracadabra\nI wanna reach out and grab ya\n\nMagic, I got magic\nBaby, can you imagine?\nAll the magic we could have');

-- ============================================
-- ЗАВЕРШЕНИЕ
-- ============================================

-- Проверка созданных таблиц
SELECT 'Database tables created successfully!' AS status;
SELECT TABLE_NAME, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'j27119254_song'
ORDER BY TABLE_NAME;

-- Вывод информации о созданном администраторе
SELECT 'Admin user created:' AS info;
SELECT id, username, created_at FROM admin_users;

-- Вывод статистики
SELECT 'Database Statistics:' AS info;
SELECT
    (SELECT COUNT(*) FROM singer) AS singers,
    (SELECT COUNT(*) FROM songs) AS songs,
    (SELECT COUNT(*) FROM albums) AS albums,
    (SELECT COUNT(*) FROM textsong) AS lyrics,
    (SELECT COUNT(*) FROM admin_users) AS admins;
