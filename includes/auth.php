<?php
require_once 'db.php';
require_once 'User.php';
require_once 'functions.php';

function loginUser($username, $password) {
    $conn = getDbConnection();
    $user = new User($conn);
    
    $userData = $user->getUserByUsername($username);
    
    if ($userData && password_verify($password, $userData['password'])) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['is_admin'] = $user->isAdmin($userData['id']);
        $_SESSION['staff_id'] = $userData['staff_id'];
        $_SESSION['first_name'] = $userData['first_name'];
        $_SESSION['last_name'] = $userData['last_name'];
        return true;
    }
    
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}