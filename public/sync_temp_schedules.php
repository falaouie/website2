<?php
// File: sync_temp_schedules.php

require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    
    $query = "SELECT staff_id, date, scheduled_in, scheduled_out, day_off, open_schedule, reason FROM temp_schedule";
    $stmt = $conn->prepare($query);
    
    $stmt->execute();
    
    $temp_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $temp_schedules]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Closing the connection
$conn = null;
?>