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
        $_SESSION['user_roles'] = $user->getUserRoles($userData['id']);
        return true;
    }
    
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function hasRole($role) {
    return in_array($role, $_SESSION['user_roles'] ?? []);
}