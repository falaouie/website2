<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/auth.php';
requireLogin();

// if (!isAdmin()) {
//     redirectTo('dashboard.php');
// }

$user = new User(getDbConnection());
$isAdmin = $_SESSION['username'] === 'admin';

if ($isAdmin) {
    $adminFirstName = $user->getAdminFirstName();
} else {
    $firstName = $_SESSION['first_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Silver System</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <?php if ($isAdmin): ?>
                <div class="user-greeting">Hi <?php echo htmlspecialchars($adminFirstName); ?></div>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-primary">DASHBOARD</a>
            <?php endif; ?>
            <div><h1 class="dashboard-title">ADMIN DASHBOARD</h1></div>
            <div><a href="logout.php" class="btn btn-danger">LOGOUT</a></div>
        </header>
        <div class="btn-grid">
            <a href="manage_staff.php" class="btn btn-primary">Manage Staff</a>
            <a href="manage_users.php" class="btn btn-primary">Manage Users</a>
            <a href="#" class="btn btn-primary">Manage Titles</a>
            <a href="#" class="btn btn-primary">View Logs</a>
        </div>
    </div>
    <script src="./assets/js/app.js"></script>
</body>
</html>