<?php
/**
 * Admin Logout
 * Завершение сессии и выход из админ-панели
 */

session_start();

// Уничтожение всех данных сессии
$_SESSION = array();

// Удаление cookie сессии
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Уничтожение сессии
session_destroy();

// Перенаправление на страницу входа
header("Location: admin_login.php");
exit();
?>
