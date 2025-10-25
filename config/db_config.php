<?php
/**
 * Database Configuration File
 * Централизованное подключение к базе данных для всего проекта SONGLY
 */

// Параметры подключения к базе данных
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'songsbook');

// Создание подключения
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Установка кодировки UTF-8
$conn->set_charset("utf8mb4");

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

/**
 * Функция для безопасного закрытия соединения
 */
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// Автоматическое закрытие соединения при завершении скрипта
register_shutdown_function('closeConnection');
?>
