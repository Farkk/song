<?php
// Подключение к базе данных
require_once('config/db_config.php');

// Получение ID исполнителя из URL
if (isset($_GET['id'])) {
    $singer_id = $_GET['id'];

    // Проверка, что ID является числом (для безопасности)
    if (!is_numeric($singer_id)) {
        echo "Некорректный ID исполнителя.";
        exit();
    }

    // Запрос для получения информации об исполнителе
    $sql_singer = "SELECT * FROM singer WHERE id_singer = ?";
    $stmt_singer = $conn->prepare($sql_singer);

    if ($stmt_singer) { // Проверка на успешность подготовки
        $stmt_singer->bind_param("i", $singer_id);
        $stmt_singer->execute();
        $result_singer = $stmt_singer->get_result();
        $singer_info = $result_singer->fetch_assoc();
        $stmt_singer->close(); 
    } else {
        echo "Ошибка подготовки запроса информации об исполнителе: " . $conn->error;
        exit();
    }
    if ($singer_info) {
        // Запрос для получения популярных песен исполнителя
        $sql_songs = "SELECT * FROM songs WHERE id_singer = ? ORDER BY number_of_auditions DESC LIMIT 4"; // получаем 4 самые популярные песни
        $stmt_songs = $conn->prepare($sql_songs);

        if ($stmt_songs) { // Проверка на успешность подготовки
            $stmt_songs->bind_param("i", $singer_id);
            $stmt_songs->execute();
            $result_songs = $stmt_songs->get_result();
            $stmt_songs->close(); // Закрываем оператор
        } else {
            echo "Ошибка подготовки запроса песен: " . $conn->error;
            $result_songs = false; 
        }

        // Запрос для получения популярных альбомов исполнителя
        $sql_albums = "SELECT * FROM albums WHERE id_singer = ? ORDER BY year_of_release DESC LIMIT 4"; // получаем 4 последних альбома
        $stmt_albums = $conn->prepare($sql_albums);

        if ($stmt_albums) { // Проверка на успешность подготовки
            $stmt_albums->bind_param("i", $singer_id);
            $stmt_albums->execute();
            $result_albums = $stmt_albums->get_result();
            $stmt_albums->close(); // Закрываем оператор
        } else {
            echo "Ошибка подготовки запроса альбомов: " . $conn->error;
            $result_albums = false;
        }

    } else {
        echo "Исполнитель не найден.";
        exit(); // Прерываем выполнение скрипта
    }
} else {
    echo "Не указан ID исполнителя.";
    exit(); // Прерываем выполнение скриpta
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"/>
    <link href="./css/main.css" rel="stylesheet"/>
    <link href="./css/style.css" rel="stylesheet"/>
    <title><?php echo htmlspecialchars($singer_info['name_singer']); ?></title> <!-- Динамический заголовок -->
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
            </div>
            <hr class="divider">
        </div>
    </header>
    <main>
        <div class="image-container">
            <?php
            // Используем путь из базы данных
            $singer_image_path = !empty($singer_info['image_path']) ? $singer_info['image_path'] : 'images/default_singer.png';
            if (file_exists($singer_image_path)) {
                echo '<img src="' . htmlspecialchars($singer_image_path) . '" alt="' . htmlspecialchars($singer_info['name_singer']) . '" class="singer-image">';
            } else {
                echo '<img src="images/default_singer.png" alt="Default Singer Image" class="singer-image">';
            }
            ?>
        </div>
        <div class="description-singer">
            <div class="left-list-container">
                <h1 class="singer-name"><?php echo htmlspecialchars($singer_info['name_singer']); ?></h1>
                <p1 class="singer-followers"><?php echo number_format($singer_info['followers'], 0, '.', ','); ?></p1>
                <p class="description"><?php echo nl2br(htmlspecialchars($singer_info['description'])); ?></p> 
            </div>
            <div class="right-list-container">
                <div class="item-container">
                    <div class="item">
                        <img src="images/featured-icon.png" alt="Featured" class="icon">
                        <span class="text">featured</span>
                    </div>
                    <div class="item">
                        <img src="images/followers-icon.png" alt="Followers" class="icon">
                        <span class="text"><?php echo number_format($singer_info['followers'], 0, '.', ','); ?> followers</span>
                    </div>
                    <div class="item">
                        <img src="images/contributions-icon.png" alt="Contributions" class="icon">
                        <span class="text"><?php echo number_format($singer_info['contributions'], 0, '.', ','); ?> contributions</span>
                    </div>
                </div>
                <h1 class="section-name">POPULAR <?php echo strtoupper(htmlspecialchars($singer_info['name_singer'])); ?> SONGS</h1> <!-- указывае имя исполнителя в заголовке -->
                <div class="songs-container">
                    <?php
                    // Проверка, что запрос песен был успешным и есть результаты
                    if ($result_songs && $result_songs->num_rows > 0) {
                        while($row_song = $result_songs->fetch_assoc()) {
                            ?>
                            <div class="song-block">
                                <?php
                                // Используем путь из базы данных
                                $song_cover_path = !empty($row_song['image_path']) ? $row_song['image_path'] : 'images/default_song_cover.png';
                                if (file_exists($song_cover_path)) {
                                    echo '<img src="' . htmlspecialchars($song_cover_path) . '" alt="' . htmlspecialchars($row_song['name_songs']) . '" class="song-image">';
                                } else {
                                    echo '<img src="images/default_song_cover.png" alt="Default Song Cover" class="song-image">';
                                }
                                ?>
                                <div class="song-info">
                                    <span class="song-title"><?php echo htmlspecialchars($row_song['name_songs']); ?></span>
                                    <span class="artist-name"><?php echo htmlspecialchars($singer_info['name_singer']); ?></span>
                                    <div class="listens-info">
                                        <img src="images/views-icon.png" alt="Listens Icon" class="listens-icon">
                                        <span class="listens-count"><?php echo number_format($row_song['number_of_auditions'], 0, '.', ','); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Нет популярных песен для отображения.</p>";
                    }
                    ?>
                </div>
                <h1 class="section-name">POPULAR <?php echo strtoupper(htmlspecialchars($singer_info['name_singer'])); ?> ALBUMS</h1> <!-- Имя исполнителя в заголовке -->
                <div class="albums-container">
                    <?php
                     // Проверка, что запрос альбомов был успешным и есть результаты
                    if ($result_albums && $result_albums->num_rows > 0) {
                        while($row_album = $result_albums->fetch_assoc()) {
                            ?>
                            <div class="album-block">
                                 <?php
                                // Используем путь из базы данных
                                $album_cover_path = !empty($row_album['image_path']) ? $row_album['image_path'] : 'images/default_album_cover.png';
                                if (file_exists($album_cover_path)) {
                                    echo '<img src="' . htmlspecialchars($album_cover_path) . '" alt="' . htmlspecialchars($row_album['name_albums']) . '" class="album-image">';
                                } else {
                                    echo '<img src="images/default_album_cover.png" alt="Default Album Cover" class="album-image">';
                                }
                                ?>
                                <div class="song-info">
                                    <span class="song-title"><?php echo htmlspecialchars($row_album['name_albums']); ?></span>
                                    <span class="artist-name"><?php echo htmlspecialchars($singer_info['name_singer']); ?></span>
                                    <div class="listens-info">
                                        <span class="listens-count"><?php echo htmlspecialchars($row_album['year_of_release']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Нет популярных альбомов для отображения.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php
$conn->close();
?>