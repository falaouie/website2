<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/db.php';

$user = new User(getDbConnection());

if ($user->isUserTableEmpty()) {
    redirectTo('admin_setup.php');
} elseif (isLoggedIn()) {
    redirectTo('dashboard.php');
} else {
    redirectTo('login.php');
}