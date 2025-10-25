<?php
require_once('auth_check.php');
require_once('../config/db_config.php');

$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? '';

// Get all songs with their lyrics (if exist)
$sql = "SELECT s.id_songs, s.name_songs, singer.name_singer, ts.producer, ts.textsong
        FROM songs s
        LEFT JOIN singer ON s.id_singer = singer.id_singer
        LEFT JOIN textsong ts ON s.id_songs = ts.id_songs
        ORDER BY s.name_songs ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление текстами песен - SONGLY Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php" style="font-family: 'Krona One', sans-serif;">SONGLY ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_singers.php">Исполнители</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_songs.php">Песни</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_albums.php">Альбомы</a></li>
                    <li class="nav-item"><a class="nav-link active" href="admin_lyrics.php">Тексты песен</a></li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3">Привет, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="admin_logout.php" class="btn btn-outline-light btn-sm">Выйти</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Управление текстами песен</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($success_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?php echo htmlspecialchars($error_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название песни</th>
                            <th>Исполнитель</th>
                            <th>Producer</th>
                            <th>Статус текста</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_songs']; ?></td>
                                <td><?php echo htmlspecialchars($row['name_songs']); ?></td>
                                <td><?php echo htmlspecialchars($row['name_singer'] ?? 'Не указан'); ?></td>
                                <td><?php echo htmlspecialchars($row['producer'] ?? '-'); ?></td>
                                <td>
                                    <?php if (!empty($row['textsong'])): ?>
                                        <span class="badge bg-success">Есть текст</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Нет текста</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <a href="admin_lyrics_edit.php?id=<?php echo $row['id_songs']; ?>" class="btn btn-sm btn-primary">
                                        <?php echo !empty($row['textsong']) ? 'Редактировать' : 'Добавить текст'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Нет песен для управления текстами.</p>
                <a href="admin_songs.php" class="btn btn-primary">Перейти к песням</a>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">← Назад к Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
