# 🎵 SONGLY - Song Lyrics Platform

Веб-платформа для просмотра текстов песен с полнофункциональной админ-панелью.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📋 Описание

SONGLY - это PHP-приложение для каталогизации и отображения текстов песен. Проект включает:

- 🎤 **Каталог исполнителей** с фотографиями и описаниями
- 🎵 **База песен** с обложками и текстами
- 💿 **Коллекция альбомов** с годами выпуска
- 📝 **Тексты песен** с информацией о продюсерах
- 🔐 **Админ-панель** для управления всем контентом

## ✨ Основные возможности

### Для посетителей:
- Просмотр списка исполнителей и их песен
- Чтение текстов песен
- Информация об альбомах
- Статистика прослушиваний
- Responsive дизайн

### Для администраторов:
- ✅ Полное CRUD управление (Create, Read, Update, Delete)
- ✅ Загрузка и управление изображениями
- ✅ Безопасная аутентификация
- ✅ Bootstrap интерфейс
- ✅ Статистика и аналитика

## 🚀 Быстрый старт

### Требования

- PHP 7.4 или выше
- MySQL 5.7 или выше
- Web-сервер (Apache/Nginx) или встроенный PHP сервер

### Установка

1. **Клонируйте репозиторий:**

```bash
git clone https://github.com/Farkk/song.git
cd song
```

2. **Создайте базу данных:**

```bash
mysql -u root -p < database_full_setup.sql
```

3. **Настройте подключение:**

Отредактируйте `config/db_config.php` при необходимости:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Ваш пароль
define('DB_NAME', 'j27119254_song');
```

4. **Запустите сервер:**

```bash
php -S localhost:8000
```

5. **Откройте в браузере:**

- Сайт: `http://localhost:8000/index.html`
- Админка: `http://localhost:8000/admin/admin_login.php`

### Данные для входа в админ-панель:

```
Логин: admin
Пароль: admin123
```

⚠️ **Важно:** Смените пароль после первого входа!

## 📁 Структура проекта

```
songsbook_project/
├── admin/                      # Админ-панель
│   ├── admin_dashboard.php     # Главная страница админки
│   ├── admin_singers.php       # Управление исполнителями
│   ├── admin_songs.php         # Управление песнями
│   ├── admin_albums.php        # Управление альбомами
│   ├── admin_lyrics.php        # Управление текстами
│   ├── css/                    # Стили админки
│   └── includes/               # Утилиты (загрузка файлов)
├── config/                     # Конфигурация
│   └── db_config.php           # Подключение к БД
├── css/                        # Стили фронтенда
├── images/                     # Изображения
│   ├── singer/                 # Фото исполнителей
│   ├── song/                   # Обложки песен
│   └── album/                  # Обложки альбомов
├── index.html                  # Главная страница
├── singer.php                  # Страница исполнителя
├── text_song.php              # Страница с текстом песни
├── singers_list.php           # Список исполнителей
├── songs_list.php             # Список песен
├── database_full_setup.sql    # Полная установка БД
└── database_migration.sql     # Миграция для существующей БД
```

## 🔒 Безопасность

- ✅ Prepared statements (защита от SQL-инъекций)
- ✅ Password hashing (bcrypt)
- ✅ XSS защита (htmlspecialchars)
- ✅ Session timeout (30 минут)
- ✅ Валидация загружаемых файлов
- ✅ Проверка типов данных

## 📖 Документация

- **[INSTALL.md](INSTALL.md)** - Пошаговая инструкция установки
- **[README_ADMIN.md](README_ADMIN.md)** - Полное руководство по админ-панели
- **[CLAUDE.md](CLAUDE.md)** - Техническая документация для разработчиков

## 🛠️ Технологии

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+ (InnoDB, utf8mb4)
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **UI Framework:** Bootstrap 5.3.0
- **Fonts:** Google Fonts (Krona One)

## 📸 Скриншоты

### Главная страница
Отображение топ-чартов песен с возможностью "Load More"

### Админ-панель
- Dashboard с статистикой
- CRUD интерфейсы для всех сущностей
- Загрузка и предпросмотр изображений
- Responsive таблицы

## 🤝 Вклад в проект

Contributions приветствуются! Пожалуйста:

1. Fork репозиторий
2. Создайте feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit изменения (`git commit -m 'Add some AmazingFeature'`)
4. Push в branch (`git push origin feature/AmazingFeature`)
5. Откройте Pull Request

## 📝 Лицензия

Этот проект распространяется под лицензией MIT. См. файл `LICENSE` для подробностей.

## 👨‍💻 Автор

**Pavel**
- GitHub: [@Farkk](https://github.com/Farkk)

## 🙏 Благодарности

- Bootstrap команде за отличный UI фреймворк
- Claude Code за помощь в разработке админ-панели
- Сообществу PHP разработчиков

---

**⭐ Если проект оказался полезным, поставьте звезду!**

🤖 *Сгенерировано с помощью [Claude Code](https://claude.com/claude-code)*
