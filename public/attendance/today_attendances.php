<?php

date_default_timezone_set('Asia/Beirut');

session_start();

// require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

// requireLogin();

if (isset($_SESSION['selected_date'])) {
    unset($_SESSION['selected_date']); // used for temporary schedule in schedules.php
}

$user = new User(getDbConnection());

if (isset($_SESSION['first_name'])) {
    $firstName = $_SESSION['first_name'];
}

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

    <?php
        if (isset($_SESSION['first_name'])) {
            ?>
            <header>

                <div>

                    <a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a>

                    <a href="attendance.php" class="btn btn-primary">ATTENDANCE</a>

                </div>



                <div>

                    <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

                </div>

            </header>
            <?php
        }
    ?>

        <div class="dashboard-container">

            <div>

                <h1 class="dashboard-title">Staff Attendance</h1>

            </div>

            <table class="schedule-table">
                <thead>
                    <tr>
                        <th colspan='6'>
                            <div id="clock">&nbsp;</div>
                        </th>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <th>Scheduled In</th>
                        <th>WORK IN</th>
                        <th>Scheduled Out</th>
                        <th>WORK OFF</th>
                        <th>HOURS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): 
                        $staffId = $schedule['staff_id'];
                        
                        // Fetch attendance for this staff member
                        $attendance = $user->getAttendanceForDay($staffId, $today);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name'][0]); ?></td>
                            
                            <!-- Scheduled In -->
                            <?php
                                if ($schedule['day_off'] == 1) {
                                    ?>
                                    <td colspan="5">
                                        DAY OFF
                                    </td>
                                    <?php
                                } elseif ($schedule['open_schedule'] == 1) {
                                    ?>
                                    <td>
                                        OPEN
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td>
                                        <?php
                                        echo !empty($schedule['start_time']) ? htmlspecialchars(formatTime($schedule['start_time'])) : '';
                                        ?>
                                    </td>
                                    <?php
                                } 
                                
                            ?>

                            <!-- Work In -->
                            <?php
                                if (!empty($schedule['start_time']) && !empty($attendance['work_in']) && ($schedule['open_schedule'] != 1 || $schedule['day_off'] != 1)) {
                                    if (strtotime('+1 minute', strtotime($schedule['start_time'])) > strtotime($attendance['work_in'])) {
                                        ?>
                                        <td class="greenText">
                                                <!-- Display Work In time if already clocked in -->
                                                <?php echo htmlspecialchars(formatTime($attendance['work_in'])); ?>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="redText">
                                                <!-- Display Work In time if already clocked in -->
                                                <?php echo htmlspecialchars(formatTime($attendance['work_in'])); ?>

                                        </td>
                                        <?php
                                    }
                                } else {
                                    if ((!empty($schedule['start_time']) || $schedule['open_schedule'] == 1) && empty($attendance['work_in']) && $schedule['day_off'] != 1) {
                                        ?>
                                    <td>
                                        <!-- Show Work In Button if not yet clocked in -->
                                        <form method="POST" action="">
                                            <input type="hidden" name="staff_id" value="<?php echo $staffId; ?>">
                                            <button type="submit" name="work_in" class="btn btn-primary">Work In</button>
                                        </form>
                                    </td>
                                    <?php
                                    } elseif (!empty($attendance['work_in']) && $schedule['day_off'] != 1) {
                                        ?>
                                        <td>
                                            <?php
                                            echo htmlspecialchars(formatTime($attendance['work_in']));  
                                            ?>
                                        </td>
                                        <?php
                                    } elseif ($schedule['day_off'] != 1){
                                        ?>
                                        <td>
                                        &nbsp;
                                        </td>
                                        <?php
                                    }
                                    
                                }
                            ?>
                            

                            <!-- Scheduled Out -->
                            <?php
                                if ($schedule['day_off'] != 1) {
                                    if ($schedule['open_schedule'] == 1) {
                                        ?>
                                        <td>
                                            OPEN
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td>
                                            <?php
                                            echo !empty($schedule['end_time']) ? htmlspecialchars(formatTime($schedule['end_time'])) : '';
                                            ?>
                                        </td>
                                        <?php
                                    } 
                                }
                            ?>

                            <!-- Work Off -->
                            
                            <?php
                                if (!empty($attendance['work_off']) && ($schedule['open_schedule'] != 1 || $schedule['day_off'] != 1)) {
                                    if ($schedule['open_schedule'] != 1 && $schedule['day_off'] != 1) {
                                        if ((strtotime('+1 minute', strtotime($schedule['end_time'])) > strtotime($attendance['work_off'])) && $schedule['open_schedule'] != 1) {
                                            ?>
                                            <td class="redText">
                                                    <!-- Display Work Off time if already clocked out -->
                                                    <?php echo htmlspecialchars(formatTime($attendance['work_off'])); ?>
                                            </td>
                                            <?php
                                        } else {
                                            ?>
                                            <td class="greenText">
                                                <!-- Display Work Off time if already clocked out -->
                                                <?php echo htmlspecialchars(formatTime($attendance['work_off'])); ?>
                                            </td>
                                            <?php
                                        } 
                                    } else {
                                        ?>
                                        <td>
                                            <!-- Display Work Off time if already clocked out -->
                                            <?php echo htmlspecialchars(formatTime($attendance['work_off'])); ?>
                                        </td>
                                        <?php
                                    }
                                } else {
                                    if (!empty($attendance['work_in']) && $schedule['end_time'] != 'OPEN' && $schedule['end_time'] != 'DAY OFF') {
                                        ?>
                                        <td>
                                            <!-- Show Work Off Button if not yet clocked out -->
                                            <form method="POST" action="">
                                                <input type="hidden" name="staff_id" value="<?php echo $staffId; ?>">
                                                <button type="submit" name="work_off" class="btn btn-primary">Work Off</button>
                                            </form>
                                        </td>
                                        <?php
                                    } elseif ($schedule['day_off'] != 1){
                                        ?>
                                        <td>
                                        &nbsp;
                                        </td>
                                        <?php
                                    }
                                    
                                }
                            ?>
                            <!-- Hours Worked -->
                            <?php
                                if ($schedule['day_off'] != 1) {
                                    ?>
                                    <td>
                                        <?php if (!empty($attendance['hours_worked'])): ?>
                                            <?php echo htmlspecialchars($attendance['hours_worked']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php
                                }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        
        function startClock() {
            // Get the time element
            var clock = document.getElementById('clock');

            // Initialize the time from the PHP value
            var currentTime = '<?php echo $current_time; ?>';
            var timeParts = currentTime.split(':');
            var hours = parseInt(timeParts[0], 10);
            var minutes = parseInt(timeParts[1], 10);
            var seconds = parseInt(timeParts[2], 10);
            let ampmUpdated = false;
            let ampm;
            // Function to update the clock every second
            function updateClock() {
                
                seconds++;

                if (seconds >= 60) {
                    seconds = 0;
                    minutes++;
                }

                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                }

                if (hours >= 24) {
                    hours = 0;
                }

                if (ampmUpdated == false) {
                   // Format hours, minutes, and seconds to always show two digits
                    ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // Convert hour '0' to '12' 
                    ampmUpdated = true;
                }
                
                hours = hours < 10 ? ('0' + hours).slice(-1) : ('0' + hours).slice(-2);
                var formattedTime = 
                    hours + ':' + 
                    ('0' + minutes).slice(-2) + ':' + 
                    ('0' + seconds).slice(-2) + ' ' + ampm;

                // Update the HTML element with the new time
                clock.textContent = formattedTime;
            }

            // Update the clock every second
            setInterval(updateClock, 1000);
        }

        // Run the startClock function when the page loads
        window.onload = startClock;
    </script>
    
</body>


</html>