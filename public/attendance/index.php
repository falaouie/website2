<?php
session_start();
require_once '../../includes/functions.php';
require_once '../../includes/User.php';
require_once '../../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Silver System</title>
    <link rel="stylesheet" href="../../public/assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div><a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a></div>
            <div><h1>Attendance</h1></div>
            <div><a href="logout.php" class="btn btn-danger">LOGOUT</a></div>
        </header>

        <div>Attendance</div>
    </div>
    <script src="../../public/assets/js/app.js"></script>
</body>
</html>