<?php
/**
 * Delete Singer
 * Удаление исполнителя
 */

require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$singer_id = $_GET['id'] ?? null;

// Проверка ID
if (!$singer_id || !is_numeric($singer_id)) {
    header("Location: admin_singers.php?error=Неверный ID исполнителя");
    exit();
}

// Получение данных исполнителя
$stmt = $conn->prepare("SELECT name_singer, image_path FROM singer WHERE id_singer = ?");
$stmt->bind_param("i", $singer_id);
$stmt->execute();
$result = $stmt->get_result();
$singer = $result->fetch_assoc();
$stmt->close();

if (!$singer) {
    header("Location: admin_singers.php?error=Исполнитель не найден");
    exit();
}

// Проверка на связанные записи (песни и альбомы)
$check_songs = $conn->prepare("SELECT COUNT(*) as count FROM songs WHERE id_singer = ?");
$check_songs->bind_param("i", $singer_id);
$check_songs->execute();
$songs_count = $check_songs->get_result()->fetch_assoc()['count'];
$check_songs->close();

$check_albums = $conn->prepare("SELECT COUNT(*) as count FROM albums WHERE id_singer = ?");
$check_albums->bind_param("i", $singer_id);
$check_albums->execute();
$albums_count = $check_albums->get_result()->fetch_assoc()['count'];
$check_albums->close();

if ($songs_count > 0 || $albums_count > 0) {
    $error = "Невозможно удалить исполнителя {$singer['name_singer']}, так как у него есть {$songs_count} песен и {$albums_count} альбомов. Сначала удалите связанные записи.";
    header("Location: admin_singers.php?error=" . urlencode($error));
    exit();
}

// Удаление исполнителя
$delete_stmt = $conn->prepare("DELETE FROM singer WHERE id_singer = ?");
$delete_stmt->bind_param("i", $singer_id);

if ($delete_stmt->execute()) {
    // Удаление изображения, если оно существует
    if (!empty($singer['image_path'])) {
        deleteImage($singer['image_path']);
    }

    $delete_stmt->close();
    header("Location: admin_singers.php?success=Исполнитель успешно удален");
    exit();
} else {
    $delete_stmt->close();
    header("Location: admin_singers.php?error=Ошибка при удалении исполнителя");
    exit();
}
?>
