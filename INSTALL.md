# 🚀 SONGLY - Быстрая установка

## Шаг 1: Создание базы данных

### Вариант А: Через командную строку

```bash
mysql -u root -p < database_full_setup.sql
```

Введите пароль MySQL (если есть) и нажмите Enter.

### Вариант Б: Через phpMyAdmin

1. Откройте phpMyAdmin в браузере
2. Нажмите на вкладку "SQL"
3. Откройте файл `database_full_setup.sql` в текстовом редакторе
4. Скопируйте весь код и вставьте в окно SQL
5. Нажмите кнопку "Вперед" (Go)

### Вариант В: Через MySQL Workbench

1. Откройте MySQL Workbench
2. Подключитесь к серверу MySQL
3. File → Open SQL Script → выберите `database_full_setup.sql`
4. Нажмите иконку молнии (Execute) или Ctrl+Shift+Enter

## Шаг 2: Проверка конфигурации

Откройте файл `config/db_config.php` и убедитесь, что настройки соответствуют вашему MySQL серверу:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // Ваш пароль MySQL
define('DB_NAME', 'songsbook');
```

## Шаг 3: Запуск проекта

### Вариант А: Встроенный PHP сервер

```bash
cd /путь/к/songsbook_project
php -S localhost:8000
```

Откройте браузер: `http://localhost:8000`

### Вариант Б: XAMPP/MAMP

1. Скопируйте папку проекта в `htdocs` (XAMPP) или `htdocs` (MAMP)
2. Запустите Apache и MySQL через панель управления
3. Откройте браузер: `http://localhost/songsbook_project/`

## 🎯 Доступ к админ-панели

**URL:** `http://localhost:8000/admin/admin_login.php`

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
