<?php
// Запуск сессии для авторизации
session_start();

// Подключение к базе данных
require_once('config/db_config.php');

// Подключение к базе данных пользователей через PDO
require_once('config/db.php');

// Проверка авторизации и получение данных пользователя
$user = null;
if (isset($_SESSION['user_id']) && isset($_SESSION['authenticated'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Получение топ песен по количеству прослушиваний
$sql = "SELECT s.id_songs, s.name_songs, s.number_of_auditions, s.image_path,
               singer.id_singer, singer.name_singer
        FROM songs s
        LEFT JOIN singer ON s.id_singer = singer.id_singer
        ORDER BY s.number_of_auditions DESC";

$result = $conn->query($sql);

// Функция для форматирования количества прослушиваний
function formatViews($number) {
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'k';
    }
    return number_format($number);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"/>
    <link href="./css/main.css" rel="stylesheet"/>
    <title>SONGLY</title>
</head>
<body>
    <header class="site-header">
        <div class="inner-content">
            <div class="site-name">
                <a href="index.php" class="main-text">SONGLY</a>
            </div>
            <hr class="divider">
            <div class="links-container">
                <a href="songs_list.php" class="link">songs</a>
                <a href="singers_list.php" class="link">singers</a>
                <?php if ($user): ?>
                    <span class="link user-name"><?php echo htmlspecialchars($user['first_name']); ?></span>
                    <a href="auth/logout.php" class="link logout-link" title="Выход">✕</a>
                <?php else: ?>
                    <a href="auth/login.php" class="link">войти</a>
                <?php endif; ?>
            </div>
            <hr class="divider">
        </div>
    </header>
    <main>
        <div class="main_heading">
            <h1>NEWS</h1>
        </div>
        <img src="images/main.png" alt="Main Image" class="main-image">
        <div class="main_heading">
            <h1>CHARTS</h1>
        </div>
    </main>
    <footer>
        <?php
        if ($result && $result->num_rows > 0) {
            $position = 1;
            while ($row = $result->fetch_assoc()) {
                $hiddenClass = ($position > 3) ? 'hidden' : '';
                $imagePath = !empty($row['image_path']) ? $row['image_path'] : 'images/default_song_cover.png';

                // Проверка существования файла
                if (!file_exists($imagePath)) {
                    $imagePath = 'images/default_song_cover.png';
                }
                ?>
                <div class="song-item-container <?php echo $hiddenClass; ?>">
                    <div class="item-number"><?php echo $position; ?></div>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>"
                         alt="<?php echo htmlspecialchars($row['name_songs']); ?>"
                         class="song-cover">
                    <a href="text_song.php?id=<?php echo $row['id_songs']; ?>"
                       class="song-title"><?php echo htmlspecialchars($row['name_songs']); ?></a>
                    <div class="lyrics-label">Lyrics</div>
                    <a href="singer.php?id=<?php echo $row['id_singer']; ?>"
                       class="artist-name"><?php echo htmlspecialchars($row['name_singer'] ?? 'Unknown Artist'); ?></a>
                    <img src="images/views-icon.png" alt="Views Icon" class="views-icon">
                    <div class="views-count"><?php echo formatViews($row['number_of_auditions']); ?></div>
                </div>
                <?php
                $position++;
            }
        } else {
            echo '<div class="empty-state"><p>Нет песен для отображения. Добавьте песни через админ-панель.</p></div>';
        }
        ?>

        <?php if ($result && $result->num_rows > 3): ?>
            <button class="load-more-button" id="loadMoreBtn">Load More</button>
            <button class="hide-button hidden" id="hideBtn">Hide</button>
        <?php endif; ?>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const hideBtn = document.getElementById('hideBtn');
            const hiddenSongs = document.querySelectorAll('.song-item-container.hidden');

            if (loadMoreBtn && hideBtn) {
                loadMoreBtn.addEventListener('click', () => {
                    hiddenSongs.forEach(song => {
                        song.classList.remove('hidden');
                    });
                    loadMoreBtn.classList.add('hidden');
                    hideBtn.classList.remove('hidden');
                });

                hideBtn.addEventListener('click', () => {
                    document.querySelectorAll('.song-item-container').forEach((song, index) => {
                        if (index >= 3) {
                            song.classList.add('hidden');
                        }
                    });
                    loadMoreBtn.classList.remove('hidden');
                    hideBtn.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
