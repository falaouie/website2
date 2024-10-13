<?php
session_start();
require_once '../../includes/functions.php';
require_once '../../includes/User.php';
require_once '../../includes/auth.php';
requireLogin();

function formatTime($time) {
    if (empty($time)) return '';
    $timestamp = strtotime($time);
    return date('h:i A', $timestamp);
}

$user = new User(getDbConnection());
$staffWithAttendance = $user->getStaffWithAttendanceRequired();
$currentSchedules = $user->getCurrentFixedSchedules();
$reasons = $user->getReasons();

// Determine the selected date, default to today on initial load
$selectedDate = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$temporarySchedules = $user->getTemporarySchedules($selectedDate);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['schedule_type']) && $_POST['schedule_type'] === 'temp') {
        $result = $user->updateTemporarySchedule($_POST);
    } else {
        $result = $user->updateFixedSchedule($_POST);
    }
    $message = $result === true ? "Schedules updated successfully." : "Error: " . $result;
    
    // Refresh the temporary schedules after update
    $temporarySchedules = $user->getTemporarySchedules($selectedDate);
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Fixed and Temporary Schedules - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .schedule-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .schedule-table th, .schedule-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .schedule-table th { background-color: #f2f2f2; }
        .staff-name { text-align: left; }
        .day-off { color: red; }
        .hidden { display: none; }
        
        /* Toggle styles */
        .schedule-option {
            background: orange;
            padding: 10px 20px;
            outline: 1px solid black;
            margin: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            user-select: none;
        }
        .schedule-option.selected { background: limegreen; }
        input[type="radio"] { display: none; }
        #fixedScheduleForm, #tempScheduleForm {
            display: none;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div>
                <a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a>
                <a href="attendance.php" class="btn btn-primary">ATTENDANCE</a>
            </div>
            <div>
                <h1 class="dashboard-title">Staff Schedule</h1>
            </div>
            <div>
                <a href="../logout.php" class="btn btn-danger">LOGOUT</a>
            </div>
        </header>

        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="center-container">
            <div id="fixedSchdDiv" class="schedule-option selected">
                <strong><label for="fixedSchd">FIXED</label></strong>
                <input class="screen-only" id="fixedSchd" type="radio" name="staffSchd" value="fixed" checked>
            </div>
            <div id="tempSchdDiv" class="schedule-option">
                <strong><label for="tempSchd">TEMPORARY</label></strong>
                <input class="screen-only" id="tempSchd" type="radio" name="staffSchd" value="temp">
            </div>
        </div>

        <!-- Fixed Schedule Form  -->
        <form id="fixedScheduleForm" method="post" action="" style="display: block;">
            <input type="hidden" name="schedule_type" value="fixed">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <?php foreach ($days as $day): ?>
                            <th colspan="2"><?php echo $day; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th></th>
                        <?php foreach ($days as $day): ?>
                            <th>From</th>
                            <th>To</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentSchedules as $staff): ?>
                        <tr>
                            <td class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></td>
                            <?php foreach ($days as $index => $day): ?>
                                <?php
                                $schedule = $staff['schedule'][$index] ?? null;
                                $isOpen = $schedule['open_schedule'] ?? false;
                                $isDayOff = $schedule['day_off'] ?? false;
                                $startTime = $schedule['start'] ?? '';
                                $endTime = $schedule['end'] ?? '';
                                ?>
                                <td <?php echo $isDayOff ? 'class="day-off"' : ($isOpen ? 'class="open-schedule"' : ''); ?>>
                                    <?php
                                    if ($isOpen) {
                                        echo "Open";
                                    } elseif ($isDayOff) {
                                        echo "DAY OFF";
                                    } else {
                                        echo htmlspecialchars(formatTime($startTime));
                                    }
                                    ?>
                                </td>
                                <td <?php echo $isDayOff ? 'class="day-off"' : ($isOpen ? 'class="open-schedule"' : ''); ?>>
                                    <?php
                                    if ($isOpen) {
                                        echo "Open";
                                    } elseif ($isDayOff) {
                                        echo "DAY OFF";
                                    } else {
                                        echo htmlspecialchars(formatTime($endTime));
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <!-- Temporary Schedule Form -->
        <form id="tempScheduleForm" method="post" action="">
            <input type="hidden" name="schedule_type" value="temp">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>From Time</th>
                        <th>To Time</th>
                        <th>Reason</th>
                        <th>Fixed Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffWithAttendance as $staff): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td><?php echo htmlspecialchars(formatTime($temporarySchedules[$staff['id']]['from_time'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(formatTime($temporarySchedules[$staff['id']]['to_time'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($temporarySchedules[$staff['id']]['reason'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(formatTime($currentSchedules[$staff['id']]['start_time'] ?? '')) . ' - ' . htmlspecialchars(formatTime($currentSchedules[$staff['id']]['end_time'] ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fixedScheduleForm = document.getElementById('fixedScheduleForm');
            const tempScheduleForm = document.getElementById('tempScheduleForm');
            const fixedSchdDiv = document.getElementById('fixedSchdDiv');
            const tempSchdDiv = document.getElementById('tempSchdDiv');

            fixedSchdDiv.addEventListener('click', function() {
                fixedScheduleForm.style.display = 'block';
                tempScheduleForm.style.display = 'none';
                fixedSchdDiv.classList.add('selected');
                tempSchdDiv.classList.remove('selected');
            });

            tempSchdDiv.addEventListener('click', function() {
                fixedScheduleForm.style.display = 'none';
                tempScheduleForm.style.display = 'block';
                fixedSchdDiv.classList.remove('selected');
                tempSchdDiv.classList.add('selected');
            });
        });
    </script>
</body>
</html>