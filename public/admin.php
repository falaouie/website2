<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
requireLogin();

$user = new User(getDbConnection());
$roles = $user->getUserRoles($_SESSION['user_id']);

if (!in_array('admin', $roles)) {
    redirectTo('dashboard.php');
}

// Admin functionality will be added here
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
            <h1>Admin Panel</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <h2>Admin Functions</h2>
            <!-- Admin functions will be added here -->
            <ul>
                <li><a href="#" id="manage-users">Manage Users</a></li>
                <li><a href="#" id="system-settings">System Settings</a></li>
            </ul>
        </main>
    </div>
    <footer>
        <p>&copy; Copyright 2023 Silver System</p>
    </footer>
    <script src="../assets/js/app.js"></script>
</body>
</html>