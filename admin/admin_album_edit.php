<?php
require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$error_message = '';
$album_id = $_GET['id'] ?? null;

if (!$album_id || !is_numeric($album_id)) {
    header("Location: admin_albums.php?error=Неверный ID альбома");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM albums WHERE id_albums = ?");
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();
$album = $result->fetch_assoc();
$stmt->close();

if (!$album) {
    header("Location: admin_albums.php?error=Альбом не найден");
    exit();
}

$singers_result = $conn->query("SELECT id_singer, name_singer FROM singer ORDER BY name_singer ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name_albums'] ?? '');
    $id_singer = intval($_POST['id_singer'] ?? 0);
    $year = intval($_POST['year_of_release'] ?? date('Y'));

    if (empty($name)) {
        $error_message = 'Название альбома обязательно.';
    } elseif ($id_singer <= 0) {
        $error_message = 'Выберите исполнителя.';
    } else {
        $image_path = $album['image_path'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['image'], 'album', $album_id);
            if ($uploadResult['success']) {
                if (!empty($album['image_path'])) {
                    deleteImage($album['image_path']);
                }
                $image_path = $uploadResult['path'];
            } else {
                $error_message = $uploadResult['message'];
            }
        }

        if (empty($error_message)) {
            $update_stmt = $conn->prepare("UPDATE albums SET name_albums = ?, id_singer = ?, year_of_release = ?, image_path = ? WHERE id_albums = ?");
            $update_stmt->bind_param("siisi", $name, $id_singer, $year, $image_path, $album_id);

            if ($update_stmt->execute()) {
                $update_stmt->close();
                header("Location: admin_albums.php?success=Альбом успешно обновлен");
                exit();
            } else {
                $error_message = 'Ошибка при обновлении альбома: ' . $conn->error;
            }
            $update_stmt->close();
        }
    }

    $album['name_albums'] = $name;
    $album['id_singer'] = $id_singer;
    $album['year_of_release'] = $year;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать альбом - SONGLY Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">SONGLY ADMIN</a>
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm">Выйти</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Редактировать альбом</h1>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Название альбома *</label>
                        <input type="text" class="form-control" name="name_albums" required value="<?php echo htmlspecialchars($album['name_albums']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Исполнитель *</label>
                        <select class="form-select" name="id_singer" required>
                            <?php
                            while($singer = $singers_result->fetch_assoc()) {
                                $selected = ($album['id_singer'] == $singer['id_singer']) ? 'selected' : '';
                                echo '<option value="' . $singer['id_singer'] . '" ' . $selected . '>' . htmlspecialchars($singer['name_singer']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Год выпуска</label>
                        <input type="number" class="form-control" name="year_of_release" min="1900" max="2100" value="<?php echo htmlspecialchars($album['year_of_release']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Обложка альбома</label>
                        <?php if (!empty($album['image_path'])): ?>
                            <div class="mb-2"><img src="../<?php echo htmlspecialchars($album['image_path']); ?>" class="image-preview"></div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a href="admin_albums.php" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
