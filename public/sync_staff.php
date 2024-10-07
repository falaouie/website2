<?php
// File: sync_staff.php

require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    
    $query = "SELECT staff_id, first_name, last_name FROM staff_tbl WHERE status = :status AND attendance_req = :attendance_req";
    $stmt = $conn->prepare($query);
    
    // Binding parameters with placeholders
    $stmt->bindValue(':status', 1, PDO::PARAM_INT);
    $stmt->bindValue(':attendance_req', 1, PDO::PARAM_INT);
    
    $stmt->execute();
    
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $staff]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Closing the connection
$conn = null;
?>