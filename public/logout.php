<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $user = new User(getDbConnection());
    $user->logLogout($_SESSION['user_id']);
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page
redirectTo('login.php');