<?php

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();

if (isset($_SESSION['selected_date'])) {
    unset($_SESSION['selected_date']); // used for temporary schedule in schedules.php
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

            <div><h1 class="dashboard-title">ATTENDANCE MANAGEMENT</h1></div>

            <div>

                <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

            </div>

        </header>



        <div class="btn-grid">

            <a href="attendance.php" class="btn btn-primary">Attendance</a>

            <a href="schedules.php" class="btn btn-primary">Schedules</a>

            <a href="attendance_report.php" class="btn btn-primary">Attendance History</a>

        </div>

    </div>

    <script src="../assets/js/app.js"></script>

</body>

</html>