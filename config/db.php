<?php
/**
 * Конфигурация подключения к базе данных для авторизации пользователей
 * Использует PDO для работы с MySQL
 */

$host = 'songbooks.asmart-test-dev.ru';
$dbname = 'j27119254_song';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
