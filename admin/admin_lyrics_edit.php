<?php
require_once('auth_check.php');
require_once('../config/db_config.php');

$error_message = '';
$song_id = $_GET['id'] ?? null;

if (!$song_id || !is_numeric($song_id)) {
    header("Location: admin_lyrics.php?error=Неверный ID песни");
    exit();
}

// Get song info
$stmt = $conn->prepare("SELECT s.*, singer.name_singer FROM songs s LEFT JOIN singer ON s.id_singer = singer.id_singer WHERE s.id_songs = ?");
$stmt->bind_param("i", $song_id);
$stmt->execute();
$song = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$song) {
    header("Location: admin_lyrics.php?error=Песня не найдена");
    exit();
}

// Get lyrics if exist
$lyrics_stmt = $conn->prepare("SELECT * FROM textsong WHERE id_songs = ?");
$lyrics_stmt->bind_param("i", $song_id);
$lyrics_stmt->execute();
$lyrics = $lyrics_stmt->get_result()->fetch_assoc();
$lyrics_stmt->close();

// Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producer = trim($_POST['producer'] ?? '');
    $textsong = trim($_POST['textsong'] ?? '');

    if (empty($textsong)) {
        $error_message = 'Текст песни не может быть пустым.';
    } else {
        if ($lyrics) {
            // Update existing
            $update_stmt = $conn->prepare("UPDATE textsong SET producer = ?, textsong = ? WHERE id_songs = ?");
            $update_stmt->bind_param("ssi", $producer, $textsong, $song_id);
        } else {
            // Insert new
            $update_stmt = $conn->prepare("INSERT INTO textsong (id_songs, producer, textsong) VALUES (?, ?, ?)");
            $update_stmt->bind_param("iss", $song_id, $producer, $textsong);
        }

        if ($update_stmt->execute()) {
            $update_stmt->close();
            header("Location: admin_lyrics.php?success=Текст песни успешно сохранен");
            exit();
        } else {
            $error_message = 'Ошибка при сохранении текста: ' . $conn->error;
        }
        $update_stmt->close();
    }

    // Update data for display
    $lyrics['producer'] = $producer;
    $lyrics['textsong'] = $textsong;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать текст песни - SONGLY Admin</title>
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
        <h1 class="mb-4">Редактировать текст песни</h1>

        <div class="alert alert-info">
            <strong>Песня:</strong> <?php echo htmlspecialchars($song['name_songs']); ?><br>
            <strong>Исполнитель:</strong> <?php echo htmlspecialchars($song['name_singer'] ?? 'Не указан'); ?>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Producer</label>
                        <input type="text" class="form-control" name="producer" value="<?php echo htmlspecialchars($lyrics['producer'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Текст песни *</label>
                        <textarea class="form-control" name="textsong" rows="20" required><?php echo htmlspecialchars($lyrics['textsong'] ?? ''); ?></textarea>
                        <div class="form-text">Введите текст песни. Используйте Enter для разделения строк.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Сохранить текст</button>
                        <a href="admin_lyrics.php" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
