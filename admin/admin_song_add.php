<?php
/**
 * Add New Song
 * Форма добавления новой песни
 */

require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$error_message = '';

// Получение списка исполнителей для выпадающего списка
$singers_result = $conn->query("SELECT id_singer, name_singer FROM singer ORDER BY name_singer ASC");

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name_songs'] ?? '');
    $id_singer = intval($_POST['id_singer'] ?? 0);
    $number_of_auditions = intval($_POST['number_of_auditions'] ?? 0);

    // Валидация
    if (empty($name)) {
        $error_message = 'Название песни обязательно для заполнения.';
    } elseif ($id_singer <= 0) {
        $error_message = 'Пожалуйста, выберите исполнителя.';
    } else {
        // Вставка в базу данных
        $stmt = $conn->prepare("INSERT INTO songs (name_songs, id_singer, number_of_auditions, image_path) VALUES (?, ?, ?, ?)");

        $image_path = null;

        $stmt->bind_param("siis", $name, $id_singer, $number_of_auditions, $image_path);

        if ($stmt->execute()) {
            $song_id = $stmt->insert_id;

            // Обработка загрузки изображения
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = uploadImage($_FILES['image'], 'song', $song_id);

                if ($uploadResult['success']) {
                    $update_stmt = $conn->prepare("UPDATE songs SET image_path = ? WHERE id_songs = ?");
                    $update_stmt->bind_param("si", $uploadResult['path'], $song_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
            }

            $stmt->close();
            header("Location: admin_songs.php?success=Песня успешно добавлена");
            exit();
        } else {
            $error_message = 'Ошибка при добавлении песни: ' . $conn->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить песню - SONGLY Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php" style="font-family: 'Krona One', sans-serif;">SONGLY ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_singers.php">Исполнители</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_songs.php">Песни</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_albums.php">Альбомы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_lyrics.php">Тексты песен</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">
                        Привет, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                    <a href="admin_logout.php" class="btn btn-outline-light btn-sm">Выйти</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1 class="mb-4">Добавить новую песню</h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name_songs" class="form-label">Название песни *</label>
                        <input type="text" class="form-control" id="name_songs" name="name_songs" required
                               value="<?php echo htmlspecialchars($_POST['name_songs'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="id_singer" class="form-label">Исполнитель *</label>
                        <select class="form-select" id="id_singer" name="id_singer" required>
                            <option value="">-- Выберите исполнителя --</option>
                            <?php
                            if ($singers_result && $singers_result->num_rows > 0) {
                                while($singer = $singers_result->fetch_assoc()) {
                                    $selected = (isset($_POST['id_singer']) && $_POST['id_singer'] == $singer['id_singer']) ? 'selected' : '';
                                    echo '<option value="' . $singer['id_singer'] . '" ' . $selected . '>' .
                                         htmlspecialchars($singer['name_singer']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <?php if ($singers_result && $singers_result->num_rows == 0): ?>
                            <div class="form-text text-danger">
                                Нет доступных исполнителей. <a href="admin_singer_add.php">Добавьте исполнителя</a> сначала.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="number_of_auditions" class="form-label">Количество прослушиваний</label>
                        <input type="number" class="form-control" id="number_of_auditions" name="number_of_auditions" min="0"
                               value="<?php echo htmlspecialchars($_POST['number_of_auditions'] ?? '0'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Обложка песни</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <div class="form-text">Форматы: JPG, PNG, GIF. Максимальный размер: 5MB</div>
                        <div id="imagePreview"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Добавить песню</button>
                        <a href="admin_songs.php" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
