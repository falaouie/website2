<?php

require_once __DIR__ . '/../config/database.php';

// date_default_timezone_set('Asia/Beirut');

function getDbConnection() {

    try {

        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



        // Set the timezone to Asia/Beirut

        // $conn->exec("SET time_zone = '+03:00'");
        // $conn->exec("SET time_zone = 'Asia/Beirut'");



        return $conn;

    } catch(PDOException $e) {

        die("Connection failed: " . $e->getMessage());

    }

}

