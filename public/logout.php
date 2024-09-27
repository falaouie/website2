<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../config/database.php';
require_once '../includes/db.php';

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    $user = new User(getDbConnection());
    $user->logActivity($_SESSION['user_id'], 'logout');
}

// Destroy the session
session_destroy();

// Redirect to the login page
redirectTo('login.php');