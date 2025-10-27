<?php
/**
 * Конфигурация для VK ID авторизации
 */

// ID приложения VK (замените на ваш реальный ID)
define('VK_APP_ID', 52860889);

// Защищенный ключ приложения (замените на ваш реальный ключ)
define('VK_APP_SECRET', 'ваш_защищенный_ключ');

// URL для редиректа после авторизации
define('VK_REDIRECT_URI', 'https://songbooks.asmart-test-dev.ru/auth/vk_callback.php');
