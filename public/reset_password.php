<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/User.php';

if (!isset($_SESSION['reset_user'])) {
    redirectTo('login.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $user = new User(getDbConnection());
        $result = $user->updatePassword($_SESSION['reset_user'], $newPassword);
        
        if ($result === true) {
            unset($_SESSION['reset_user']);
            redirectTo('dashboard.php');
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="reset-password-page">
        <div class="reset-password-container">
            <h2>Reset Password</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="reset_password.php" method="post">
                <div class="input-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>