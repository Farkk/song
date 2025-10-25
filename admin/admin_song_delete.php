<?php
/**
 * Delete Song
 * Удаление песни
 */

require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$song_id = $_GET['id'] ?? null;

if (!$song_id || !is_numeric($song_id)) {
    header("Location: admin_songs.php?error=Неверный ID песни");
    exit();
}

// Получение данных песни
$stmt = $conn->prepare("SELECT name_songs, image_path FROM songs WHERE id_songs = ?");
$stmt->bind_param("i", $song_id);
$stmt->execute();
$result = $stmt->get_result();
$song = $result->fetch_assoc();
$stmt->close();

if (!$song) {
    header("Location: admin_songs.php?error=Песня не найдена");
    exit();
}

// Проверка на наличие текста песни
$check_lyrics = $conn->prepare("SELECT COUNT(*) as count FROM textsong WHERE id_songs = ?");
$check_lyrics->bind_param("i", $song_id);
$check_lyrics->execute();
$lyrics_count = $check_lyrics->get_result()->fetch_assoc()['count'];
$check_lyrics->close();

// Если есть текст - сначала удаляем его
if ($lyrics_count > 0) {
    $delete_lyrics = $conn->prepare("DELETE FROM textsong WHERE id_songs = ?");
    $delete_lyrics->bind_param("i", $song_id);
    $delete_lyrics->execute();
    $delete_lyrics->close();
}

// Удаление песни
$delete_stmt = $conn->prepare("DELETE FROM songs WHERE id_songs = ?");
$delete_stmt->bind_param("i", $song_id);

if ($delete_stmt->execute()) {
    // Удаление изображения
    if (!empty($song['image_path'])) {
        deleteImage($song['image_path']);
    }

    $delete_stmt->close();
    header("Location: admin_songs.php?success=Песня успешно удалена");
    exit();
} else {
    $delete_stmt->close();
    header("Location: admin_songs.php?error=Ошибка при удалении песни");
    exit();
}
?>
