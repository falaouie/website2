<?php

date_default_timezone_set('Asia/Beirut');

function sanitizeInput($input) {

    return htmlspecialchars(strip_tags(trim($input)));

}



function redirectTo($location) {

    header("Location: $location");

    exit;

}



function isLoggedIn() {

    return isset($_SESSION['user_id']);

}



function requireLogin() {

    if (!isLoggedIn()) {

        redirectTo('/login.php');

    }

}



function generateCSRFToken() {

  if (!isset($_SESSION['csrf_token'])) {

      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

  }

  return $_SESSION['csrf_token'];

}



function validateCSRFToken($token) {

  if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {

      return false;

  }

  return true;

}



function checkRateLimit($username) {

  $max_attempts = 5;

  $lockout_time = 15 * 60; // 15 minutes



  if (!isset($_SESSION['login_attempts'][$username])) {

      $_SESSION['login_attempts'][$username] = ['count' => 0, 'time' => time()];

  }



  $attempts = &$_SESSION['login_attempts'][$username];



  if (time() - $attempts['time'] > $lockout_time) {

      $attempts['count'] = 0;

      $attempts['time'] = time();

  }



  if ($attempts['count'] >= $max_attempts) {

      return false;

  }



  return true;

}



function incrementLoginAttempts($username) {

  if (!isset($_SESSION['login_attempts'][$username])) {

      $_SESSION['login_attempts'][$username] = ['count' => 0, 'time' => time()];

  }



  $_SESSION['login_attempts'][$username]['count']++;

}



function resetLoginAttempts($username) {

  if (isset($_SESSION['login_attempts'][$username])) {

      unset($_SESSION['login_attempts'][$username]);

  }

}

function formatTime($time) {
    if (empty($time)) return '';
    $timestamp = strtotime($time);
    return date('h:i A', $timestamp);
}