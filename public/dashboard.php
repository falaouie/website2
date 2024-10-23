<?php

session_start();

require_once '../includes/functions.php';

require_once '../includes/User.php';

require_once '../includes/auth.php';

requireLogin();



if ($_SESSION['username'] == 'admin') {

    redirectTo('/admin/admin.php');

}



$firstName = $_SESSION['first_name'];

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Silver System</title>

    <link rel="stylesheet" href="./assets/css/styles.css">

</head>

<body>

    <div class="dashboard-container">

        <header>

            <div class="user-greeting">Hi <?php echo htmlspecialchars("$firstName"); ?></div>

            <div><h1 class="dashboard-title">DASHBOARD</h1></div>

            <div><a href="logout.php" class="btn btn-danger">LOGOUT</a></div>

        </header>

        <div class="btn-grid">

            <a href="./sales/index.php" class="btn btn-primary">SALES & ORDERS</a>

            <a href="#" class="btn btn-primary">PAYMENTS & CREDITS</a>

            <a href="#" class="btn btn-primary">PREVIOUS INVOICES</a>

            <a href="#" class="btn btn-primary">LABELS</a>

            <a href="#" class="btn btn-primary">KITCHEN</a>

            <a href="./attendance/attendance.php" class="btn btn-primary">ATTENDANCE</a>

            <a href="#" class="btn btn-primary">PRODUCTS</a>

            <a href="#" class="btn btn-primary">PURCHASES & EXPENSES</a>

            <a href="#" class="btn btn-primary">STATEMENTS</a>

            <a href="#" class="btn btn-primary">CUSTOMERS</a>

            <a href="./admin/admin.php" class="btn btn-primary">ADMIN</a>

            <a href="#" class="btn btn-primary">BACK OFFICE</a>

        </div>

    </div>

    <script src="./assets/js/app.js"></script>

</body>

</html>