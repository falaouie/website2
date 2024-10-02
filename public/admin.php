<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/db.php';
requireLogin();

$user = new User(getDbConnection());
$roles = $user->getUserRoles($_SESSION['user_id']);

if (!in_array('admin', $roles)) {
    redirectTo('dashboard.php');
}

$username = $_SESSION['username'];
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
            <div class="user-greeting"><a href="dashboard.php" class="btn btn-primary">DASHBOARD</a></div>
            <div class="branch-buttons">
                <!-- Add branch buttons here if needed -->
            </div>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
        </header>
        <h1 class="dashboard-title">ADMIN PANEL</h1>
        <div class="btn-grid">
            <a href="#" id="manage-users" class="btn btn-primary">Manage Users</a>
            <a href="#" id="system-settings" class="btn btn-primary">System Settings</a>
            <!-- Add more admin functions as needed -->
        </div>
    </div>
    <footer>
        <p>&copy; Copyright 2023 Silver System</p>
    </footer>
    <script src="../assets/js/app.js"></script>
</body>
</html>