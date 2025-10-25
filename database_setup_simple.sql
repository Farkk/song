-- ============================================
-- SONGLY - Упрощенный скрипт установки
-- Для использования в phpMyAdmin
-- ============================================

-- ИНСТРУКЦИЯ:
-- 1. Откройте phpMyAdmin
-- 2. Выберите базу данных j27119254_song слева
-- 3. Перейдите на вкладку "SQL"
-- 4. Скопируйте и вставьте этот скрипт
-- 5. Нажмите кнопку "Вперед"

USE j27119254_song;

-- Удаление таблиц если они существуют (для чистой установки)
DROP TABLE IF EXISTS textsong;
DROP TABLE IF EXISTS albums;
DROP TABLE IF EXISTS songs;
DROP TABLE IF EXISTS singer;
DROP TABLE IF EXISTS admin_users;

-- 1. Таблица исполнителей
CREATE TABLE singer (
  id_singer INT(11) NOT NULL AUTO_INCREMENT,
  name_singer VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  followers INT(11) DEFAULT 0,
  contributions INT(11) DEFAULT 0,
  image_path VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id_singer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Таблица песен
CREATE TABLE songs (
  id_songs INT(11) NOT NULL AUTO_INCREMENT,
  name_songs VARCHAR(255) NOT NULL,
  id_singer INT(11) NOT NULL,
  number_of_auditions INT(11) DEFAULT 0,
  image_path VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id_songs),
  FOREIGN KEY (id_singer) REFERENCES singer(id_singer) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Таблица альбомов
CREATE TABLE albums (
  id_albums INT(11) NOT NULL AUTO_INCREMENT,
  name_albums VARCHAR(255) NOT NULL,
  id_singer INT(11) NOT NULL,
  year_of_release INT(4) DEFAULT NULL,
  image_path VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id_albums),
  FOREIGN KEY (id_singer) REFERENCES singer(id_singer) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Таблица текстов песен
CREATE TABLE textsong (
  id_songs INT(11) NOT NULL,
  producer VARCHAR(255) DEFAULT NULL,
  textsong TEXT NOT NULL,
  PRIMARY KEY (id_songs),
  FOREIGN KEY (id_songs) REFERENCES songs(id_songs) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Таблица администраторов
CREATE TABLE admin_users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавление администратора
-- Логин: admin, Пароль: admin123
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Добавление тестовых данных

-- Исполнители
INSERT INTO singer (name_singer, description, followers, contributions, image_path) VALUES
('Kendrick Lamar', 'American rapper, songwriter, and record producer.', 14200000, 150, 'images/singer/1.png'),
('PARTYNEXTDOOR', 'Canadian R&B singer, songwriter, and record producer.', 1593000, 85, 'images/singer/2.png'),
('Lady Gaga', 'American singer, songwriter, and actress.', 8213000, 320, 'images/singer/3.png');

-- Песни
INSERT INTO songs (name_songs, id_singer, number_of_auditions, image_path) VALUES
('Not Like Us', 1, 14200000, 'images/song/1.png'),
('MEET YOUR PADRE', 2, 159300, 'images/song/13.png'),
('Abracadabra', 3, 821300, 'images/song/10.png');

-- Альбомы
INSERT INTO albums (name_albums, id_singer, year_of_release, image_path) VALUES
('Mr. Morale & The Big Steppers', 1, 2022, 'images/album/1.png'),
('PARTYNEXTDOOR TWO', 2, 2014, 'images/album/2.png'),
('Chromatica', 3, 2020, 'images/album/3.png');

-- Тексты песен
INSERT INTO textsong (id_songs, producer, textsong) VALUES
(1, 'DJ Mustard', 'I see dead people\nI see dead people\n\nThey not like us, they not like us\nThey not like us, they not like us'),
(2, 'PARTYNEXTDOOR', 'Yeah, yeah\nMeet your padre\nMeet your padre now\n\nI''m the one you need'),
(3, 'BloodPop', 'Abracadabra\nI wanna reach out and grab ya\n\nMagic, I got magic\nBaby, can you imagine?');

-- Готово!
-- Теперь можете войти в админ-панель:
-- URL: http://localhost:8000/admin/admin_login.php
-- Логин: admin
-- Пароль: admin123
