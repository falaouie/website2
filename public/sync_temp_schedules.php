<?php
// File: sync_temp_schedules.php
date_default_timezone_set('Asia/Beirut');

require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $conn = getDbConnection();
    
    // Get today's date in 'Y-m-d' format using Asia/Beirut timezone
    $today = date('Y-m-d');
    
    // Query to fetch rows with today's date
    $query = "SELECT staff_id, date, scheduled_in, scheduled_out, day_off, open_schedule, reason_id 
              FROM temp_schedule 
              WHERE DATE(date) = :today";
              
    $stmt = $conn->prepare($query);
    
    // Bind the PHP-calculated date to the SQL query
    $stmt->bindParam(':today', $today);
    
    $stmt->execute();
    
    $temp_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $temp_schedules]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Closing the connection
$conn = null;
?>
