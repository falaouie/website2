<?php

date_default_timezone_set('Asia/Beirut');

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();

if (isset($_SESSION['selected_date'])) {
    unset($_SESSION['selected_date']); // used for temporary schedule in schedules.php
}

$user = new User(getDbConnection());

$firstName = $_SESSION['first_name'];

$today = date('Y-m-d');

$schedules = $user->getScheduleForDay($today);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['work_in'])) {
      $staff_id = $_POST['staff_id'];
      $user->recordWorkIn($staff_id); // Call the function to record work in
      header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page to show the updated data
      exit;
  }

  if (isset($_POST['work_off'])) {
      $staff_id = $_POST['staff_id'];
      $user->recordWorkOff($staff_id); // Call the function to record work off
      header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page to show the updated data
      exit;
  }
}



// Get the current time in 'H:i:s' format
$current_time = date('H:i:s');
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staff Attendance - Silver System</title>

    <link rel="stylesheet" href="../assets/css/styles.css">

</head>

<body>

    <div class="dashboard-container">

      <header>

        <div>

            <a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a>

            <a href="attendance.php" class="btn btn-primary">ATTENDANCE</a>

        </div>

        

        <div>

            <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

        </div>

      </header>

        <div class="dashboard-container">
        
            <div>

                <h1 class="dashboard-title">Attendance History</h1>

            </div>


        </div>

    </div>

    <!-- <script src="../assets/js/app.js"></script> -->
    
</body>


</html>