<?php
// Общий хедер для всех страниц сайта

// Запуск сессии если еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных пользователей через PDO (если еще не подключено)
if (!isset($pdo)) {
    require_once(__DIR__ . '/../config/db.php');
}

// Проверка авторизации и получение данных пользователя
$user = null;
if (isset($_SESSION['user_id']) && isset($_SESSION['authenticated'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>
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
