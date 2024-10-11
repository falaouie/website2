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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $user->updateFixedSchedule($_POST);

    $message = $result === true ? "Fixed schedules updated successfully." : "Error: " . $result;

    $currentSchedules = $user->getCurrentFixedSchedules(); // Refresh schedules after update

}



$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Fixed Schedules - Silver System</title>

    <link rel="stylesheet" href="../assets/css/styles.css">

    <style>

        .schedule-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }

        .schedule-table th, .schedule-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }

        .schedule-table th { background-color: #f2f2f2; }

        .staff-name { text-align: left; }

        .day-off { color: red; }

        .hidden { display: none; }

        #scheduleOptions > div {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px;
            vertical-align: top;
        }
        #scheduleOptions label {
            display: inline-block;
            margin-right: 10px;
        }
        #scheduleOptions input[type="radio"],
        #scheduleOptions input[type="time"] {
            vertical-align: middle;
        }
        #submitButton {
            display: block;
            margin-top: 10px;
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



        <form id="scheduleForm" method="post" action="">

            <table class="schedule-table">

                <thead>

                    <tr>

                        <th>

                            <label>

                                <input type="checkbox" id="checkAllDays">All Days

                            </label>

                        </th>

                        <?php foreach ($days as $index => $day): ?>

                            <th colspan="2">

                                <label>

                                    <input type="checkbox" class="day-checkbox" name="days[]" value="<?php echo $index; ?>"> <?php echo $day; ?>

                                </label>

                            </th>

                        <?php endforeach; ?>

                    </tr>

                    <tr>

                        <th>

                            <label>

                                <input type="checkbox" id="checkAllStaff">All Staff

                            </label>

                        </th>

                        <?php foreach ($days as $day): ?>

                            <th>From</th>

                            <th>To</th>

                        <?php endforeach; ?>

                    </tr>

                </thead>

                <tbody>
                    <?php foreach ($currentSchedules as $staff): ?>
                        <tr>
                            <td class="staff-name">
                                <label>
                                    <input type="checkbox" name="staff[]" value="<?php echo $staff['staff_id']; ?>" class="staff-checkbox">
                                    <?php echo htmlspecialchars($staff['name']); ?>
                                </label>
                            </td>
                            <?php foreach ($days as $index => $day): ?>
                                <?php
                                $schedule = $staff['schedule'][$index] ?? null;
                                $isOpen = $schedule['open_schedule'] ?? false;
                                $isDayOff = $schedule['day_off'] ?? false;
                                $startTime = $schedule['start'] ?? '';
                                $endTime = $schedule['end'] ?? '';
                                ?>
                                <td <?php echo $isDayOff ? 'class="day-off"' : ''; ?>>
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
                                <td <?php echo $isDayOff ? 'class="day-off"' : ''; ?>>
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



            <div id="scheduleOptions" class="hidden">
                <div>
                    <label>Open Schedule:</label>
                    <label><input type="radio" name="open_schedule" value="0" checked> No</label>
                    <label><input type="radio" name="open_schedule" value="1"> Yes</label>
                </div>
                <div id="dayOffOption">
                    <label>Day OFF:</label>
                    <label><input type="radio" name="day_off" value="0" checked> No</label>
                    <label><input type="radio" name="day_off" value="1"> Yes</label>
                </div>
                <div id="timeInputs">
                    <label>Work IN: <input type="time" name="work_in"></label>
                    <label>Work OFF: <input type="time" name="work_off"></label>
                </div>
                <button type="submit" id="submitButton" class="btn btn-primary hidden">Update Schedules</button>
            </div>

        </form>

    </div>



    <script>

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const checkAllStaff = document.getElementById('checkAllStaff');
    const checkAllDays = document.getElementById('checkAllDays');
    const staffCheckboxes = document.querySelectorAll('.staff-checkbox');
    const dayCheckboxes = document.querySelectorAll('.day-checkbox');
    const scheduleOptions = document.getElementById('scheduleOptions');
    const openScheduleRadios = document.querySelectorAll('input[name="open_schedule"]');
    const dayOffOption = document.getElementById('dayOffOption');
    const dayOffRadios = document.querySelectorAll('input[name="day_off"]');
    const timeInputs = document.getElementById('timeInputs');
    const workIn = document.querySelector('input[name="work_in"]');
    const workOff = document.querySelector('input[name="work_off"]');
    const submitButton = document.getElementById('submitButton');

    function checkSelections() {
        const staffSelected = Array.from(staffCheckboxes).some(cb => cb.checked);
        const daySelected = Array.from(dayCheckboxes).some(cb => cb.checked);
        const openSchedule = document.querySelector('input[name="open_schedule"]:checked').value === '1';
        const dayOffNoOption = document.querySelector('input[name="day_off"][value="0"]');
        const dayOffYesOption = document.querySelector('input[name="day_off"][value="1"]');
        const dayOff = document.querySelector('input[name="day_off"]:checked').value === '1';
        const workInInput = document.querySelector('input[name="work_in"]');
        const workOffInput = document.querySelector('input[name="work_off"]');

        if (staffSelected && daySelected) {
            scheduleOptions.style.display = 'block';

            if (openSchedule) {
                // Set "No" as checked when openSchedule is true
                dayOffNoOption.checked = true;
                dayOffYesOption.disabled = true;
                dayOffNoOption.disabled = true;

                // Clear time inputs
                workInInput.value = '';
                workOffInput.value = '';

                dayOffOption.style.display = 'none';
                timeInputs.style.display = 'none';
                submitButton.style.display = 'block';
            } else {
                // Enable the day_off options when not in openSchedule
                dayOffYesOption.disabled = false;
                dayOffNoOption.disabled = false;

                dayOffOption.style.display = 'inline-block';
                if (dayOff) {
                    timeInputs.style.display = 'none';
                    submitButton.style.display = 'block';
                } else {
                    timeInputs.style.display = 'inline-block';

                    // Validate time inputs
                    if (workInInput.value && workOffInput.value) {
                        const workInTime = new Date(`1970-01-01T${workInInput.value}:00`);
                        const workOffTime = new Date(`1970-01-01T${workOffInput.value}:00`);
                        
                        // Check if work off time is earlier than work in time
                        if (workOffTime <= workInTime) {
                            alert('Work OFF time must be later than Work IN time.');
                            submitButton.style.display = 'none';
                        } else {
                            submitButton.style.display = 'block';
                        }
                    } else {
                        submitButton.style.display = 'none';
                    }
                }
            }
        } else {
            scheduleOptions.style.display = 'none';
        }
    }




    checkAllStaff.addEventListener('change', function() {
        staffCheckboxes.forEach(cb => cb.checked = this.checked);
        checkSelections();
    });

    checkAllDays.addEventListener('change', function() {
        dayCheckboxes.forEach(cb => cb.checked = this.checked);
        checkSelections();
    });

    staffCheckboxes.forEach(cb => cb.addEventListener('change', function() {
        checkAllStaff.checked = Array.from(staffCheckboxes).every(cb => cb.checked);
        checkSelections();
    }));

    dayCheckboxes.forEach(cb => cb.addEventListener('change', function() {
        checkAllDays.checked = Array.from(dayCheckboxes).every(cb => cb.checked);
        checkSelections();
    }));

    openScheduleRadios.forEach(radio => radio.addEventListener('change', checkSelections));
    dayOffRadios.forEach(radio => radio.addEventListener('change', checkSelections));

    workIn.addEventListener('input', checkSelections);
    workOff.addEventListener('input', checkSelections);

    form.addEventListener('submit', function(e) {
        // Additional form validation can be added here if needed
    });

    // Initial check
    checkSelections();
});

    </script>

</body>

</html>