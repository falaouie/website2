<?php

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

function formatTime($time) {
  if (empty($time)) return '';
  $timestamp = strtotime($time);
  return date('h:i A', $timestamp);
}

// var_dump($schedules);
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

            <h1 class="dashboard-title">Staff Attendance</h1>

        </div>

        <div>

            <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

        </div>

      </header>



        <div class="dashboard-container">

          <table class="schedule-table">
            <thead>
              <tr>
                <th colspan='6'>
                  {dynamic time}
                </th>
              </tr>
              <tr>
                <th>
                  NAME
                </th>
                <th>
                  Scheduled In
                </th>
                <th>
                  WORK IN
                </th>
                <th>
                  Scheduled Out
                </th>
                <th>
                  WORK OFF
                </th>
                <th>
                  HOURS
                </th>
              </tr>
            </thead>
            <tbody>
              
            <?php foreach ($schedules as $schedule): 
                    $staffId = $schedule['staff_id'];
                  ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']); ?></td>

                        <!-- description -->
                            <?php if ($schedule['day_off'] == 1): ?>
                                <!-- Merge the tds for Day Off -->
                                <td colspan="6">
                                    <?php
                                    if ($schedule['day_off'] == 1) {
                                        echo "DAY OFF";
                                    }
                                    ?>
                                </td>

                                <td>
                                <?php
                                    if (!empty($schedule['start_time'])) {
                                        echo htmlspecialchars(formatTime($schedule['start_time']));
                                    } else {
                                        echo ''; // Leave blank if no end_time
                                    }
                                ?>
                                </td>
                                <td>
                                    {work in button}
                                </td>
                                ?>
                            <?php endif; ?>

                        <!-- End time, day off, or open for today's schedule -->
                        <!-- description -->
                        <?php if ($schedule['day_off'] == 0 && $schedule['open_schedule'] == 0): ?>
                                <!-- Merge the tds for Day Off or Open -->
                                <td>
                                <?php
                                    if (!empty($schedule['start_time'])) {
                                        echo htmlspecialchars(formatTime($schedule['start_time']));
                                    } else {
                                        echo ''; // Leave blank if no end_time
                                    }
                                ?>
                                </td>
                                <td>
                                    {work off button}
                                </td>
                                
                            <?php endif; ?>
                            <?php if ($schedule['day_off'] == 0 && $schedule['open_schedule'] == 0): ?>
                              <td>
                                {calculated hours}
                            </td>
                            <?php endif; ?> 
                    </tr>
            <?php endforeach; ?>

            </tbody>

          </table>

        </div>

    </div>

    <!-- <script src="../assets/js/app.js"></script> -->

</body>

</html>