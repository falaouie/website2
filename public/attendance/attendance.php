<?php

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();

if (isset($_SESSION['selected_date'])) {
    unset($_SESSION['selected_date']); // used for temporary schedule in schedules.php
}

if (isset($_SESSION['fromDate']) || isset($_SESSION['toDate'])) {
    unset($_SESSION['fromDate']); // used for dates in attendance_history.php
    unset($_SESSION['toDate']);
}

$user = new User(getDbConnection());

$firstName = $_SESSION['first_name'];

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Attendance Management - Silver System</title>

    <link rel="stylesheet" href="../assets/css/styles.css">

</head>

<body>

    <div class="dashboard-container">

        <header>

            <div>

                <a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a>

            </div>

            

            <div>

                <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

            </div>

        </header>

        <div><h1 class="dashboard-title">Attendance Management</h1></div>

        <div class="btn-grid">

            <a href="today_attendances.php" class="btn btn-primary">Staff Attendance</a>

            <a href="schedules.php" class="btn btn-primary">Schedules</a>

            <a href="attendance_history.php" class="btn btn-primary">Attendance History</a>

        </div>

    </div>

    <script src="../assets/js/app.js"></script>

</body>

</html>