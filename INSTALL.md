# 🚀 SONGLY - Быстрая установка

## Шаг 1: Создание базы данных

### Вариант А: Через командную строку

```bash
mysql -u root -p < database_full_setup.sql
```

Введите пароль MySQL (если есть) и нажмите Enter.

### Вариант Б: Через phpMyAdmin (РЕКОМЕНДУЕТСЯ)

**Используйте упрощенный скрипт для phpMyAdmin:**

1. Откройте phpMyAdmin в браузере
2. **Выберите базу данных `j27119254_song` слева** (важно!)
3. Перейдите на вкладку "SQL"
4. Откройте файл **`database_setup_simple.sql`** в текстовом редакторе
5. Скопируйте весь код и вставьте в окно SQL
6. Нажмите кнопку "Вперед" (Go)

**Примечание:** Используйте `database_setup_simple.sql` вместо `database_full_setup.sql` для избежания ошибок в phpMyAdmin.

### Вариант В: Через MySQL Workbench

1. Откройте MySQL Workbench
2. Подключитесь к серверу MySQL
3. File → Open SQL Script → выберите `database_full_setup.sql`
4. Нажмите иконку молнии (Execute) или Ctrl+Shift+Enter

## Шаг 2: Проверка конфигурации

Откройте файл `config/db_config.php` и убедитесь, что настройки соответствуют вашему MySQL серверу:

```php
define('DB_SERVER', 'songbooks.asmart-test-dev.ru');
define('DB_USERNAME', 'root');  // Ваш пользователь MySQL
define('DB_PASSWORD', '');      // Ваш пароль MySQL
define('DB_NAME', 'j27119254_song');  // Имя вашей базы данных
```

**Важно:** База данных `j27119254_song` должна уже существовать на сервере MySQL!

## Шаг 3: Открытие сайта

Откройте браузер: `https://songbooks.asmart-test-dev.ru`

### Для локальной разработки:

```bash
cd /путь/к/songsbook_project
php -S localhost:8000
```

Не забудьте изменить DB_SERVER в `config/db_config.php` на 'localhost' для локальной разработки.

Откройте браузер: `http://localhost:8000`

## 🎯 Доступ к админ-панели

**URL:** `https://songbooks.asmart-test-dev.ru/admin/admin_login.php`

**Данные для входа:**
- Логин: `admin`
- Пароль: `admin123`

⚠️ **ВАЖНО:** Смените пароль после первого входа!

## ✅ Проверка установки

После входа в админ-панель вы должны увидеть:
- 3 исполнителя
- 3 песни
- 3 альбома
- 3 текста песен

Это тестовые данные для демонстрации работы системы.

## 🔧 Устранение проблем

### Ошибка подключения к базе данных
```
Solution: Проверьте config/db_config.php и убедитесь, что MySQL запущен
```

### Ошибка "Access denied for user"
```
Solution: Проверьте логин и пароль в config/db_config.php
```

### Ошибка "Database 'songsbook' doesn't exist"
```
Solution: Повторно выполните database_full_setup.sql
```

### Страница входа не открывается
```
Solution: Убедитесь, что PHP сервер запущен и путь корректен
```

## 📚 Дополнительная информация

- **Полная документация:** `README_ADMIN.md`
- **Техническая документация:** `CLAUDE.md`
- **GitHub репозиторий:** https://github.com/Farkk/song

---

**Готово!** Теперь вы можете начать работу с SONGLY 🎵
