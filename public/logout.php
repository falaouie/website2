<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $user = new User(getDbConnection());
    $reason = isset($_GET['reason']) ? $_GET['reason'] : 'manual';
    $user->logLogout($_SESSION['user_id'], $reason);
}

// Destroy the session
session_unset();
session_destroy();

// If it's an AJAX request (inactivity logout), send a JSON response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode(['status' => 'logged_out']);
    exit;
}

// For manual logout, redirect to login page
redirectTo('login.php');