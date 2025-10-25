<?php
/**
 * Edit Singer
 * Редактирование существующего исполнителя
 */

require_once('auth_check.php');
require_once('../config/db_config.php');
require_once('includes/upload_image.php');

$error_message = '';
$singer_id = $_GET['id'] ?? null;

// Проверка ID
if (!$singer_id || !is_numeric($singer_id)) {
    header("Location: admin_singers.php?error=Неверный ID исполнителя");
    exit();
}

// Получение данных исполнителя
$stmt = $conn->prepare("SELECT * FROM singer WHERE id_singer = ?");
$stmt->bind_param("i", $singer_id);
$stmt->execute();
$result = $stmt->get_result();
$singer = $result->fetch_assoc();
$stmt->close();

if (!$singer) {
    header("Location: admin_singers.php?error=Исполнитель не найден");
    exit();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name_singer'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $followers = intval($_POST['followers'] ?? 0);
    $contributions = intval($_POST['contributions'] ?? 0);

    // Валидация
    if (empty($name)) {
        $error_message = 'Имя исполнителя обязательно для заполнения.';
    } else {
        $image_path = $singer['image_path']; // Текущий путь

        // Обработка загрузки нового изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['image'], 'singer', $singer_id);

            if ($uploadResult['success']) {
                // Удаляем старое изображение, если оно было
                if (!empty($singer['image_path'])) {
                    deleteImage($singer['image_path']);
                }
                $image_path = $uploadResult['path'];
            } else {
                $error_message = $uploadResult['message'];
            }
        }

        // Обновление в базе данных
        if (empty($error_message)) {
            $update_stmt = $conn->prepare("UPDATE singer SET name_singer = ?, description = ?, followers = ?, contributions = ?, image_path = ? WHERE id_singer = ?");
            $update_stmt->bind_param("ssiisi", $name, $description, $followers, $contributions, $image_path, $singer_id);

            if ($update_stmt->execute()) {
                $update_stmt->close();
                header("Location: admin_singers.php?success=Исполнитель успешно обновлен");
                exit();
            } else {
                $error_message = 'Ошибка при обновлении исполнителя: ' . $conn->error;
            }

            $update_stmt->close();
        }
    }

    // Обновляем данные для отображения в форме
    $singer['name_singer'] = $name;
    $singer['description'] = $description;
    $singer['followers'] = $followers;
    $singer['contributions'] = $contributions;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать исполнителя - SONGLY Admin</title>
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
                        <a class="nav-link active" href="admin_singers.php">Исполнители</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_songs.php">Песни</a>
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
        <h1 class="mb-4">Редактировать исполнителя</h1>

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
                        <label for="name_singer" class="form-label">Имя исполнителя *</label>
                        <input type="text" class="form-control" id="name_singer" name="name_singer" required
                               value="<?php echo htmlspecialchars($singer['name_singer']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($singer['description'] ?? ''); ?></textarea>
                        <div class="form-text">Краткая биография или описание исполнителя</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="followers" class="form-label">Подписчики</label>
                            <input type="number" class="form-control" id="followers" name="followers" min="0"
                                   value="<?php echo htmlspecialchars($singer['followers'] ?? '0'); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contributions" class="form-label">Contributions</label>
                            <input type="number" class="form-control" id="contributions" name="contributions" min="0"
                                   value="<?php echo htmlspecialchars($singer['contributions'] ?? '0'); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Фотография исполнителя</label>

                        <?php if (!empty($singer['image_path'])): ?>
                            <div class="mb-2">
                                <p class="text-muted mb-1">Текущее изображение:</p>
                                <?php
                                $currentImage = '../' . $singer['image_path'];
                                if (file_exists($currentImage)):
                                ?>
                                    <img src="<?php echo htmlspecialchars($currentImage); ?>"
                                         alt="<?php echo htmlspecialchars($singer['name_singer']); ?>"
                                         class="image-preview">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <div class="form-text">Форматы: JPG, PNG, GIF. Максимальный размер: 5MB. Оставьте пустым, чтобы сохранить текущее изображение.</div>
                        <div id="imagePreview"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        <a href="admin_singers.php" class="btn btn-secondary">Отмена</a>
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
                    const label = document.createElement('p');
                    label.className = 'text-muted mb-1 mt-3';
                    label.textContent = 'Новое изображение:';
                    preview.appendChild(label);
                    preview.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
