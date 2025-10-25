<?php
/**
 * Admin Login Page
 * Страница авторизации для доступа к админ-панели
 */

session_start();

// Если уже авторизован, перенаправляем на dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

// Подключение к базе данных
require_once('../config/db_config.php');

$error_message = '';
$timeout_message = '';

// Проверка, истекла ли сессия
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = 'Ваша сессия истекла. Пожалуйста, войдите снова.';
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Пожалуйста, заполните все поля.';
    } else {
        // Проверка учетных данных
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Проверка пароля
            if (password_verify($password, $user['password_hash'])) {
                // Успешная авторизация
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['last_activity'] = time();

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = 'Неверное имя пользователя или пароль.';
            }
        } else {
            $error_message = 'Неверное имя пользователя или пароль.';
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель - SONGLY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #303030;
            font-family: 'Krona One', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: #424242;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
        }
        .login-title {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        .form-label {
            color: #ccc;
            font-size: 14px;
        }
        .form-control {
            background-color: #525252;
            border: 1px solid #666;
            color: white;
        }
        .form-control:focus {
            background-color: #525252;
            border-color: white;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        .btn-login {
            background-color: white;
            color: #303030;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border: none;
        }
        .btn-login:hover {
            background-color: #e0e0e0;
            color: #303030;
        }
        .alert {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">SONGLY ADMIN</h1>

        <?php if (!empty($timeout_message)): ?>
            <div class="alert alert-warning" role="alert">
                <?php echo htmlspecialchars($timeout_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">Войти</button>
        </form>

        <div class="text-center mt-4">
            <a href="../index.html" style="color: #999; text-decoration: none; font-size: 12px;">← Вернуться на сайт</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
