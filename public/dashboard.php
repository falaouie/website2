<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
requireLogin();

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="user-greeting">Hi <?php echo htmlspecialchars($username); ?></div>
            <div class="branch-buttons">
                <button class="branch-btn active">Main Branch</button>
                <button class="branch-btn">Oloa Branch</button>
            </div>
            <a href="logout.php" class="logout-btn">LOGOUT</a>
        </header>
        <h1 class="dashboard-title">DASHBOARD</h1>
        <div class="dashboard-grid">
            <button class="dashboard-btn">SALES & ORDERS</button>
            <button class="dashboard-btn">PAYMENTS & CREDITS</button>
            <button class="dashboard-btn">PREVIOUS INVOICES</button>
            <button class="dashboard-btn">LABELS</button>
            <button class="dashboard-btn">KITCHEN</button>
            <button class="dashboard-btn">ATTENDANCE</button>
            <button class="dashboard-btn">PRODUCTS</button>
            <button class="dashboard-btn">PURCHASES & EXPENSES</button>
            <button class="dashboard-btn">STATEMENTS</button>
            <button class="dashboard-btn">CUSTOMERS</button>
            <button class="dashboard-btn">ADMIN</button>
            <button class="dashboard-btn">BACK OFFICE</button>
        </div>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>