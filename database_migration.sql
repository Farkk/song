-- SONGLY Admin Panel Database Migration
-- Дата создания: 2025-10-25
-- Описание: Добавление админ-панели и поддержки путей к изображениям в БД

-- 1. Создание таблицы для администраторов
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Добавление колонок для путей к изображениям
ALTER TABLE `singer`
ADD COLUMN `image_path` VARCHAR(255) DEFAULT NULL AFTER `description`;

ALTER TABLE `songs`
ADD COLUMN `image_path` VARCHAR(255) DEFAULT NULL AFTER `number_of_auditions`;

ALTER TABLE `albums`
ADD COLUMN `image_path` VARCHAR(255) DEFAULT NULL AFTER `year_of_release`;

-- 3. Обновление существующих записей с текущими путями к изображениям
-- Для исполнителей
UPDATE `singer`
SET `image_path` = CONCAT('images/singer/', `id_singer`, '.png')
WHERE `id_singer` IS NOT NULL;

-- Для песен
UPDATE `songs`
SET `image_path` = CONCAT('images/song/', `id_songs`, '.png')
WHERE `id_songs` IS NOT NULL;

-- Для альбомов
UPDATE `albums`
SET `image_path` = CONCAT('images/album/', `id_albums`, '.png')
WHERE `id_albums` IS NOT NULL;

-- 4. Создание первого администратора
-- Логин: admin
-- Пароль: admin123
-- Хеш пароля сгенерирован с помощью password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `admin_users` (`username`, `password_hash`)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 5. Убедимся, что таблица textsong существует и связана правильно
-- Проверяем структуру textsong (если нужно, можно раскомментировать)
-- DESCRIBE textsong;

-- ПРИМЕЧАНИЯ:
-- - После выполнения этого скрипта первый администратор будет создан
-- - Данные для входа: admin / admin123
-- - Рекомендуется сменить пароль после первого входа
-- - Все существующие изображения должны быть уже загружены в соответствующие папки
-- - Если изображение отсутствует, путь все равно будет в БД, обработка на стороне PHP
