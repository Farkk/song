<?php
require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$album_id = $_GET['id'] ?? null;

if (!$album_id || !is_numeric($album_id)) {
    header("Location: admin_albums.php?error=Неверный ID альбома");
    exit();
}

$stmt = $conn->prepare("SELECT name_albums, image_path FROM albums WHERE id_albums = ?");
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();
$album = $result->fetch_assoc();
$stmt->close();

if (!$album) {
    header("Location: admin_albums.php?error=Альбом не найден");
    exit();
}

$delete_stmt = $conn->prepare("DELETE FROM albums WHERE id_albums = ?");
$delete_stmt->bind_param("i", $album_id);

if ($delete_stmt->execute()) {
    if (!empty($album['image_path'])) {
        deleteImage($album['image_path']);
    }
    $delete_stmt->close();
    header("Location: admin_albums.php?success=Альбом успешно удален");
    exit();
} else {
    $delete_stmt->close();
    header("Location: admin_albums.php?error=Ошибка при удалении альбома");
    exit();
}
?>
