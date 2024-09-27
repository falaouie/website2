<?php
session_start();
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirectTo('dashboard.php');
} else {
    redirectTo('login.php');
}