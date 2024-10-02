<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitizeInput($_POST['first_name']);
    $lastName = sanitizeInput($_POST['last_name']);
    $phoneNumber = sanitizeInput($_POST['phone_number']);
    $email = sanitizeInput($_POST['email']);

    $user = new User(getDbConnection());
    $result = $user->createAdminUser($firstName, $lastName, $phoneNumber, $email);

    if ($result === true) {
        redirectTo('login.php');
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="admin-setup-page">
        <div class="admin-setup-container">
            <h2>Admin Setup</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="admin_setup.php" method="post">
                <div class="input-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="input-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="input-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="tel" id="phone_number" name="phone_number" required>
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Create Admin</button>
            </form>
        </div>
    </div>
</body>
</html>