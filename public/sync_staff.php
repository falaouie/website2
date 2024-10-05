<?php
// File: sync_staff.php

require_once '../config/database.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    
    $query = "SELECT staff_id, first_name, last_name FROM staff_tbl WHERE status = 1 AND attendance_req = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $staff]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>