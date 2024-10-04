<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = "Invalid request";
    } else {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        $user = new User(getDbConnection());
        $loginResult = loginUser($username, $password);

        if ($loginResult === true) {
            // Log the login
            $logId = $user->logLogin($_SESSION['user_id']);
            $_SESSION['login_log_id'] = $logId;
            
            if ($user->isPasswordReset($username)) {
                $_SESSION['reset_user'] = $username;
                redirectTo('reset_password.php');
            } else {
                redirectTo('dashboard.php');
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container login-page">
        <div class="login-date-display"></div>
        <div class="login-container">
            <img src="../assets/img/silver_system_logo.png" alt="Silver System Logo" class="logo">
            <form action="login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <?php if ($error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-medium">Login</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; Copyright 2023 Silver System</p>
    </footer>
    <script src="../assets/js/app.js"></script>
</body>
</html>