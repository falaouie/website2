<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/auth.php';
requireLogin();

if (!isAdmin()) {
    redirectTo('dashboard.php');
}

$user = new User(getDbConnection());
$adminFirstName = $user->getAdminFirstName();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="user-greeting">Hi <?php echo htmlspecialchars($adminFirstName); ?></div>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
        </header>
        <h1 class="dashboard-title">ADMIN DASHBOARD</h1>
        <div class="btn-grid">
            <a href="#" class="btn btn-primary">Manage Staff</a>
            <a href="#" class="btn btn-primary">Manage Titles</a>
            <a href="#" class="btn btn-primary">Reset Passwords</a>
            <a href="#" class="btn btn-primary">View Logs</a>
        </div>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>