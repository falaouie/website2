<?php
// File: sync_schedules.php

require_once '../config/database.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    
    $query = "SELECT s.staff_id, st.first_name, st.last_name, s.work_day, s.start_time, s.end_time
              FROM schedules s
              JOIN staff_tbl st ON s.staff_id = st.staff_id
              WHERE st.status = 1 AND st.attendance_req = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $schedules]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn = null;
?>