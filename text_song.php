<?php
// Подключение к базе данных
require_once('config/db_config.php');

// Получение ID песни из URL
if (isset($_GET['id'])) {
    $song_id = $_GET['id'];

    // Проверка, что ID является числом (для безопасности)
    if (!is_numeric($song_id)) {
        echo "Некорректный ID песни.";
        exit();
    }

    // Запрос для получения информации о песне и тексте
    // Объединяем таблицы songs и textsong по id_song
    $sql_song_text = "SELECT s.name_songs, s.id_singer, s.image_path, ts.producer, ts.textsong
                      FROM songs s
                      JOIN textsong ts ON s.id_songs = ts.id_songs
                      WHERE s.id_songs = ?";
    $stmt_song_text = $conn->prepare($sql_song_text);

    if ($stmt_song_text) { // Проверка на успешность подготовки
        $stmt_song_text->bind_param("i", $song_id);
        $stmt_song_text->execute();
        $result_song_text = $stmt_song_text->get_result();
        $song_text_info = $result_song_text->fetch_assoc();
        $stmt_song_text->close(); 
    } else {
        echo "Ошибка подготовки запроса информации о песне: " . $conn->error;
        exit();
    }
    // Получение имени исполнителя 
    $singer_name = "Неизвестный исполнитель"; 
    if ($song_text_info && isset($song_text_info['id_singer'])) {
        $sql_singer_name = "SELECT name_singer FROM singer WHERE id_singer = ?";
        $stmt_singer_name = $conn->prepare($sql_singer_name);
        if ($stmt_singer_name) {
            $stmt_singer_name->bind_param("i", $song_text_info['id_singer']);
            $stmt_singer_name->execute();
            $result_singer_name = $stmt_singer_name->get_result();
            if ($row_singer_name = $result_singer_name->fetch_assoc()) {
                $singer_name = $row_singer_name['name_singer'];
            }
            $stmt_singer_name->close();
        } else {
             echo "Ошибка подготовки запроса имени исполнителя: " . $conn->error;
        }
    }
    if ($song_text_info) {
    } else {
        echo "Песня или текст не найдены.";
        exit(); // Прерываем выполнение скрипта
    }
} else {
    echo "Не указан ID песни.";
    exit(); // Прерываем выполнение скрипта
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet" />
    <link href="./css/main.css" rel="stylesheet" />
    <link href="./css/style.css" rel="stylesheet" />
    <title><?php echo htmlspecialchars($song_text_info['name_songs']); ?> - Текст песни</title> 
</head>
<body>
    <?php include('includes/header.php'); ?>
    <main>
        <div class="description-song">
            <div class="upper-container">
                <div class="imagesong">
                    <?php
                    // Используем путь из базы данных
                    $song_cover_path = !empty($song_text_info['image_path']) ? $song_text_info['image_path'] : 'images/default_song_cover.png';
                    if (file_exists($song_cover_path)) {
                        echo '<img src="' . htmlspecialchars($song_cover_path) . '" alt="' . htmlspecialchars($song_text_info['name_songs']) . '" class="image-song">';
                    } else {
                        echo '<img src="images/default_song_cover.png" alt="Default Song Cover" class="image-song">';
                    }
                    ?>
                </div>
                <div class="details-song">
                    <h1 class="song-name"><?php echo htmlspecialchars($song_text_info['name_songs']); ?></h1>
                    <h1 class="song-author"><?php echo htmlspecialchars($singer_name); ?></h1>
                    <h1 class="song-title-producer">Producer</h1>
                    <h1 class="song-producer"><?php echo htmlspecialchars($song_text_info['producer']); ?></h1>
                </div>
            </div>
            <div class="lower-container">
                <p class="text-song"><?php echo nl2br(htmlspecialchars($song_text_info['textsong'])); ?></p> 
            </div>
        </div>
    </main>
</body>
</html>
<?php
$conn->close();
?>