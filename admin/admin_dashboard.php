<?php
/**
 * Admin Dashboard
 * Главная страница админ-панели с навигацией и статистикой
 */

require_once('auth_check.php');
require_once('../config/db_config.php');

// Получение статистики
$stats = [];

// Количество исполнителей
$result = $conn->query("SELECT COUNT(*) as count FROM singer");
$stats['singers'] = $result->fetch_assoc()['count'];

// Количество песен
$result = $conn->query("SELECT COUNT(*) as count FROM songs");
$stats['songs'] = $result->fetch_assoc()['count'];

// Количество альбомов
$result = $conn->query("SELECT COUNT(*) as count FROM albums");
$stats['albums'] = $result->fetch_assoc()['count'];

// Количество текстов песен
$result = $conn->query("SELECT COUNT(*) as count FROM textsong");
$stats['lyrics'] = $result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - SONGLY</title>
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
                        <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_singers.php">Исполнители</a>
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
    <div class="container mt-5">
        <h1 class="mb-4">Панель управления</h1>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Исполнители</h5>
                        <p class="card-text display-4"><?php echo $stats['singers']; ?></p>
                        <a href="admin_singers.php" class="btn btn-light btn-sm">Управление</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Песни</h5>
                        <p class="card-text display-4"><?php echo $stats['songs']; ?></p>
                        <a href="admin_songs.php" class="btn btn-light btn-sm">Управление</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Альбомы</h5>
                        <p class="card-text display-4"><?php echo $stats['albums']; ?></p>
                        <a href="admin_albums.php" class="btn btn-dark btn-sm">Управление</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Тексты песен</h5>
                        <p class="card-text display-4"><?php echo $stats['lyrics']; ?></p>
                        <a href="admin_lyrics.php" class="btn btn-light btn-sm">Управление</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Быстрые действия</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="admin_singer_add.php" class="list-group-item list-group-item-action">
                                + Добавить нового исполнителя
                            </a>
                            <a href="admin_song_add.php" class="list-group-item list-group-item-action">
                                + Добавить новую песню
                            </a>
                            <a href="admin_album_add.php" class="list-group-item list-group-item-action">
                                + Добавить новый альбом
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link to main site -->
        <div class="mt-4 text-center">
            <a href="../index.php" class="btn btn-secondary">← Вернуться на сайт</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
