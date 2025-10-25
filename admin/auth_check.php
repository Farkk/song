<?php
/**
 * Authentication Check
 * Этот файл должен быть включен в начале каждой admin страницы
 * Проверяет, авторизован ли пользователь
 */

// Запуск сессии, если она еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Если не авторизован, перенаправляем на страницу входа
    header("Location: admin_login.php");
    exit();
}

// Проверка timeout сессии (30 минут неактивности)
$timeout_duration = 1800; // 30 минут в секундах

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Сессия истекла
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}

// Обновляем время последней активности
$_SESSION['last_activity'] = time();
?>
