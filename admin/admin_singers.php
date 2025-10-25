<?php
/**
 * Singers Management - List
 * Список всех исполнителей с возможностью редактирования и удаления
 */

require_once('auth_check.php');
require_once('../config/db_config.php');

// Обработка сообщений об успехе/ошибке
$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? '';

// Получение списка всех исполнителей
$sql = "SELECT id_singer, name_singer, image_path, followers, contributions FROM singer ORDER BY name_singer ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление исполнителями - SONGLY Admin</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Управление исполнителями</h1>
            <a href="admin_singer_add.php" class="btn btn-primary">+ Добавить исполнителя</a>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Имя</th>
                            <th>Подписчики</th>
                            <th>Contributions</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_singer']; ?></td>
                                <td>
                                    <?php
                                    $imagePath = !empty($row['image_path']) ? '../' . $row['image_path'] : '../images/default_singer.png';
                                    if (file_exists($imagePath)):
                                    ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>"
                                             alt="<?php echo htmlspecialchars($row['name_singer']); ?>"
                                             class="table-image">
                                    <?php else: ?>
                                        <img src="../images/default_singer.png" alt="No image" class="table-image">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['name_singer']); ?></td>
                                <td><?php echo number_format($row['followers'], 0, '.', ','); ?></td>
                                <td><?php echo number_format($row['contributions'], 0, '.', ','); ?></td>
                                <td class="action-buttons">
                                    <a href="admin_singer_edit.php?id=<?php echo $row['id_singer']; ?>"
                                       class="btn btn-sm btn-warning">Редактировать</a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete(<?php echo $row['id_singer']; ?>, '<?php echo htmlspecialchars($row['name_singer'], ENT_QUOTES); ?>')">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Нет исполнителей для отображения.</p>
                <a href="admin_singer_add.php" class="btn btn-primary">Добавить первого исполнителя</a>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">← Назад к Dashboard</a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Вы действительно хотите удалить исполнителя <strong id="singerName"></strong>?</p>
                    <p class="text-danger">Это действие нельзя отменить!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Удалить</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('singerName').textContent = name;
            document.getElementById('confirmDeleteBtn').href = 'admin_singer_delete.php?id=' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
