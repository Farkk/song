<?php
// Запуск сессии для авторизации
session_start();

// Подключение к базе данных
require_once('config/db_config.php');

// Получаем все песни
$sql = "SELECT id_songs, name_songs FROM songs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"/>
    <link href="./css/main.css" rel="stylesheet"/>
    <link href="./css/list.css" rel="stylesheet"/>
    <title>SONGS</title>
</head>
<body>
<?php include('includes/header.php'); ?>
<main>
    <div class="songslist">
        <h1 class="title">POPULAR SONGS</h1>
        <?php
        if ($result && $result->num_rows > 0) {
            // Выводим список песен
            while ($row = $result->fetch_assoc()) {
                $id = $row['id_songs'];
                $name = htmlspecialchars($row['name_songs']);
                echo "<a href='text_song.php?id=$id' class='namesong'>$name</a><br/>";
            }
        } else {
            echo "<p>Нет песен для отображения.</p>";
        }
        $conn->close();
        ?>
    </div>
</main>
</body>
</html>