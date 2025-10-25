<?php
// Подключение к базе данных
require_once('config/db_config.php');

// Получаем всех исполнителей
$sql = "SELECT id_singer, name_singer FROM singer";
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
    <title>SINGERS</title>
</head>
<body>
<header class="site-header">
    <div class="inner-content">
        <div class="site-name">
            <a href="index.php" class="main-text">SONGLY</a>
        </div>
        <hr class="divider" />
        <div class="links-container">
            <a href="songs_list.php" class="link">songs</a>
            <a href="singers_list.php" class="link">singers</a>
        </div>
        <hr class="divider" />
    </div>
</header>
<main>
    <div class="singerlist">
        <h1 class="title">POPULAR SINGERS</h1>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id_singer'];
                $name = htmlspecialchars($row['name_singer']);
                echo "<a href='singer.php?id=$id' class='namesinger'>$name</a><br/>";
            }
        } else {
            echo "<p>Нет исполнителей для отображения.</p>";
        }
        $conn->close();
        ?>
    </div>
</main>
</body>
</html>