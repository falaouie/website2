<?php

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();

// Check if a date has been posted or stored in the session
if (isset($_POST['temp_date'])) {
    $selectedDate = $_POST['temp_date'];
    $_SESSION['selected_date'] = $selectedDate;
} else {
    // Fall back to session value or today if nothing is posted
    $selectedDate = $_SESSION['selected_date'] ?? date('Y-m-d');
}

function formatTime($time) {
    if (empty($time)) return '';
    $timestamp = strtotime($time);
    return date('h:i A', $timestamp);
}

$user = new User(getDbConnection());

// $staffWithAttendance = $user->getStaffWithAttendanceRequired();

$currentSchedules = $user->getCurrentFixedSchedules();
$fixedSchedules = $user->getFixedSchedulesForDay($selectedDate);
$temporarySchedules = $user->getTemporarySchedules($selectedDate);
$reasons = $user->getReasons();
// Handle fixed schedule form submission separately
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fixed_schedule_update'])) {
    $result = $user->updateFixedSchedule($_POST);
    $message = $result === true ? "Fixed schedules updated successfully." : "Error: " . $result;
    $currentSchedules = $user->getCurrentFixedSchedules(); // Refresh schedules after update
}

// Handle fixed schedule form submission separately
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['temp_schedule_update'])) {
    $result = $user->updateTemporarySchedule($_POST);
    $message = $result === true ? "Temporary schedules updated successfully." : "Error: " . $result;
    $temporarySchedules = $user->getTemporarySchedules($selectedDate);// Refresh temporary schedule after update
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

        /* Style for the container to center elements */
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Style for the schedule options */
        .schedule-option {
            background: orange;
            padding: 10px 20px;
            outline: 1px solid black;
            margin: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            user-select: none;
        }

        /* Initially selected option will be limegreen */
        .schedule-option.selected {
            background: limegreen;
        }

        /* Hide the default radio button input */
        input[type="radio"] {
            display: none;
        }

        #fixedScheduleForm, #tempScheduleForm {
            display: none;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
        }

        

        .custom-time {
            display: inline-block;
            margin-right: 10px;
            font-weight: bold;
        }

        .custom-time input[type="time"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: orange;
            transition: background-color 0.3s;
        }

        .custom-time input[type="time"]:not(:placeholder-shown) {
            background-color: limegreen;
        }

        .radio-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .radio-group-label {
            margin-right: 10px;
            background-color: none;
            font-weight: bold;
        }

        .custom-radio {
            display: inline-block;
            margin-right: 5px;
            cursor: pointer;
        }

        .custom-radio input[type="radio"] {
            display: none;
        }

        .custom-radio .radio-label {
            display: inline-block;
            padding: 5px 10px;
            background-color: orange;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .custom-radio input[type="radio"]:checked + .radio-label {
            background-color: limegreen;
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
            <!-- Custom radio buttons for schedule options -->
            <div id="fixedSchdDiv" class="schedule-option">
                <strong><label for="fixedSchd">FIXED</label></strong>
                <input class="screen-only" id="fixedSchd" type="radio" name="staffSchd" value="fixed">
            </div>
            <div id="tempSchdDiv" class="schedule-option">
                <strong><label for="tempSchd">TEMPORARY</label></strong>
                <input class="screen-only" id="tempSchd" type="radio" name="staffSchd" value="temp">
            </div>

        </div>


        <form id="fixedScheduleForm" method="post" action="">
            <!-- Add a hidden input to indicate this is a schedule update form -->
            <input type="hidden" name="fixed_schedule_update" value="1">

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
                                
                                <?php if ($isOpen || $isDayOff): ?>
                                    <td colspan="2" <?php echo $isDayOff ? 'class="redText"' : ($isOpen ? 'class="greenText"' : ''); ?>>
                                        <?php
                                        if ($isOpen) {
                                            echo "Open";
                                        } elseif ($isDayOff) {
                                            echo "DAY OFF";
                                        }
                                        ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php echo htmlspecialchars(formatTime($startTime)); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(formatTime($endTime)); ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>



            <div id="scheduleOptions" class="hidden">
                <div class="radio-group">
                    <span class="radio-group-label">Open Schedule:</span>
                    <label class="custom-radio">
                        <input type="radio" name="open_schedule" value="0" checked>
                        <span class="radio-label">No</span>
                    </label>
                    <label class="custom-radio">
                        <input type="radio" name="open_schedule" value="1">
                        <span class="radio-label">Yes</span>
                    </label>
                </div>
                <div id="dayOffOption" class="radio-group">
                    <span class="radio-group-label">Day OFF:</span>
                    <label class="custom-radio">
                        <input type="radio" name="day_off" value="0" checked>
                        <span class="radio-label">No</span>
                    </label>
                    <label class="custom-radio">
                        <input type="radio" name="day_off" value="1">
                        <span class="radio-label">Yes</span>
                    </label>
                </div>
                <div id="timeInputs">
                    <label class="custom-time">
                        Work IN: <input type="time" name="work_in" placeholder=" ">
                    </label>
                    <label class="custom-time">
                        Work OFF: <input type="time" name="work_off" placeholder=" ">
                    </label>
                </div>
                <button type="submit" id="submitButton" class="btn btn-primary hidden">Update Schedules</button>
            </div>

        </form>

        <form id="tempDateForm" action="" method="post">
            <div>
                <input type="date" id="tempDate" name="temp_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                <button type="submit" id="tempLoadScheduleButton">Load Schedule</button>
            </div>
        </form>
        
        <form id="tempScheduleForm" method="post" action="">
           <!-- Add a hidden input to indicate this is a schedule update form -->
           <input type="hidden" name="temp_schedule_update" value="1">
           <input type="hidden" name="temp_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
           <table class="schedule-table">
                <thead>
                    <tr>
                        <th colspan="2">
                            &nbsp;
                        </th>
                        <th class="greenText" colspan="3">
                        <?php echo date('l d/m/Y', strtotime($selectedDate)); ?>
                        </th>
                        <th colspan="2">
                            Fixed Schedule
                        </th>
                    </tr>
                    <tr>
                        <th>Staff Name</th>
                        <th><input type="checkbox" id="tempCheckAllStaff"> All</th>
                        <th>From Time</th>
                        <th>To Time</th>
                        <th>Reason</th>
                        <th>From Time</th>
                        <th>To Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        
                        foreach ($fixedSchedules as $schedule): 
                            $staffId = $schedule['staff_id'];
                            $tempSchedule = $temporarySchedules[$staffId] ?? null; // Get temp schedule for this staff ID, if available
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']); ?></td>
                                <td><input type="checkbox" class="temp-staff-checkbox" id="tempStaff<?php echo $staffId; ?>" name="temp_staff[]" value="<?php echo $staffId; ?>"></td>

                                <!-- Temp scheduled_in and scheduled_out or merge for Day Off/Open -->
                                <?php if ($tempSchedule): ?>
                                    <?php if ($tempSchedule['day_off'] == 1 || $tempSchedule['open_schedule'] == 1): ?>
                                        <!-- Merge the tds for Day Off or Open -->
                                        <td colspan="2">
                                            <?php
                                            if ($tempSchedule['day_off'] == 1) {
                                                echo "DAY OFF";
                                            } elseif ($tempSchedule['open_schedule'] == 1) {
                                                echo "Open";
                                            }
                                            ?>
                                        </td>
                                    <?php else: ?>
                                        <!-- Show scheduled_in and scheduled_out in separate tds -->
                                        <td>
                                        <?php
                                            if (!empty($tempSchedule['scheduled_in'])) {
                                               echo htmlspecialchars(formatTime($tempSchedule['scheduled_in']));
                                            } else {
                                                echo ''; // Leave blank if no scheduled_out
                                            }
                                        ?>
                                        </td>
                                        <td>
                                            <?php
                                                if (!empty($tempSchedule['scheduled_out'])) {
                                                    echo htmlspecialchars(formatTime($tempSchedule['scheduled_out']));
                                                } else {
                                                    echo ''; // Leave blank if no scheduled_out
                                                }
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- No temp schedule, leave tds empty -->
                                    <td></td>
                                    <td></td>
                                <?php endif; ?>

                                <!-- Reason text -->
                                <td>
                                    <?php
                                    if ($tempSchedule && !empty($tempSchedule['reason_text'])) {
                                        echo htmlspecialchars($tempSchedule['reason_text']);
                                    } else {
                                        echo ''; // leave empty if no reason
                                    }
                                    ?>
                                </td>
                                
                                <!-- Start time, day off, or open for fixed schedule -->
                                <td>
                                    <?php
                                    if ($schedule['open_schedule'] == 1) {
                                        echo "Open";
                                    } elseif ($schedule['day_off'] == 1) {
                                        echo "DAY OFF";
                                    } else {
                                        if (!empty($schedule['start_time'])) {
                                            $startTime = date('g:i A', strtotime($schedule['start_time']));
                                            echo htmlspecialchars($startTime);
                                        } else {
                                            echo ''; // Leave blank if no scheduled_out
                                        }
                                    }
                                    ?>
                                </td>

                                <!-- End time, day off, or open for fixed schedule -->
                                <td>
                                    <?php
                                        if ($schedule['open_schedule'] == 1) {
                                            echo "Open";
                                        } elseif ($schedule['day_off'] == 1) {
                                            echo "DAY OFF";
                                        } else {
                                            if (!empty($schedule['end_time'])) {
                                                $endTime = date('g:i A', strtotime($schedule['end_time']));
                                                echo htmlspecialchars($endTime);
                                            } else {
                                                echo ''; // Leave blank if no scheduled_out
                                            }
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>



                </tbody>
            </table>
            
            <div id="tempScheduleOptions" class="hidden">
                <div class="radio-group">
                    <span class="radio-group-label">Open Schedule:</span>
                    <label class="custom-radio">
                        <input type="radio" name="open_schedule" value="0" checked>
                        <span class="radio-label">No</span>
                    </label>
                    <label class="custom-radio">
                        <input type="radio" name="open_schedule" value="1">
                        <span class="radio-label">Yes</span>
                    </label>
                </div>
                <div id="tempDayOffOption" class="radio-group">
                    <span class="radio-group-label">Day OFF:</span>
                    <label class="custom-radio">
                        <input type="radio" name="day_off" value="0" checked>
                        <span class="radio-label">No</span>
                    </label>
                    <label class="custom-radio">
                        <input type="radio" name="day_off" value="1">
                        <span class="radio-label">Yes</span>
                    </label>
                </div>
                <div id="tempTimeInputs">
                    <label class="custom-time">
                        Work IN: <input type="time" name="work_in" placeholder=" ">
                    </label>
                    <label class="custom-time">
                        Work OFF: <input type="time" name="work_off" placeholder=" ">
                    </label>
                </div>
                <div>
                <div>
                    <label for="temp_reason">Reason:</label>
                    <select name="temp_reason" id="temp_reason">
                        <option value="">Select Reason</option>
                        <?php foreach ($reasons as $reason) : ?>
                            <option value="<?php echo $reason['id']; ?>"><?php echo htmlspecialchars($reason['text']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                </div>
                
                <button type="submit" id="tempSubmitButton" class="btn btn-primary hidden">Update Schedules</button>
            </div>

        </form>

        

    </div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const form = document.getElementById('fixedScheduleForm');
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

            // Helper functions
            function isAnyChecked(checkboxes) {
                return Array.from(checkboxes).some(cb => cb.checked);
            }

            function areAllChecked(checkboxes) {
                return Array.from(checkboxes).every(cb => cb.checked);
            }

            function setAllCheckboxes(checkboxes, checked) {
                checkboxes.forEach(cb => cb.checked = checked);
            }

            function updateRadioStyling() {
                document.querySelectorAll('.custom-radio input[type="radio"]').forEach(radio => {
                    const label = radio.nextElementSibling;
                    if (radio.checked) {
                        label.style.backgroundColor = 'limegreen';
                    } else {
                        label.style.backgroundColor = 'white';
                    }
                });
            }

            function updateTimeInputStyling() {
                document.querySelectorAll('.custom-time input[type="time"]').forEach(input => {
                    input.style.backgroundColor = input.value ? 'limegreen' : 'orange';
                });
            }

            // Main logic
            function checkSelections() {
                const staffSelected = isAnyChecked(staffCheckboxes);
                const daySelected = isAnyChecked(dayCheckboxes);
                const openSchedule = document.querySelector('input[name="open_schedule"]:checked').value === '1';
                const dayOff = document.querySelector('input[name="day_off"]:checked').value === '1';

                scheduleOptions.style.display = staffSelected && daySelected ? 'block' : 'none';

                if (staffSelected && daySelected) {
                    const dayOffNoOption = document.querySelector('input[name="day_off"][value="0"]');
                    const dayOffYesOption = document.querySelector('input[name="day_off"][value="1"]');

                    if (openSchedule) {
                        dayOffNoOption.checked = true;
                        dayOffYesOption.disabled = dayOffNoOption.disabled = true;
                        workIn.value = workOff.value = '';
                        dayOffOption.style.display = timeInputs.style.display = 'none';
                        submitButton.style.display = 'block';
                    } else {
                        dayOffYesOption.disabled = dayOffNoOption.disabled = false;
                        dayOffOption.style.display = 'inline-block';

                        if (dayOff) {
                            timeInputs.style.display = 'none';
                            submitButton.style.display = 'block';
                            workIn.value = workOff.value = '';
                        } else {
                            timeInputs.style.display = 'inline-block';
                            validateTimeInputs();
                        }
                    }
                }

                updateRadioStyling();
                updateTimeInputStyling();
            }

            function validateTimeInputs() {
                if (workIn.value && workOff.value) {
                    const workInTime = new Date(`1970-01-01T${workIn.value}:00`);
                    const workOffTime = new Date(`1970-01-01T${workOff.value}:00`);
                    
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

            // Event listeners
            checkAllStaff.addEventListener('change', function() {
                setAllCheckboxes(staffCheckboxes, this.checked);
                checkSelections();
            });

            checkAllDays.addEventListener('change', function() {
                setAllCheckboxes(dayCheckboxes, this.checked);
                checkSelections();
            });

            staffCheckboxes.forEach(cb => cb.addEventListener('change', function() {
                checkAllStaff.checked = areAllChecked(staffCheckboxes);
                checkSelections();
            }));

            dayCheckboxes.forEach(cb => cb.addEventListener('change', function() {
                checkAllDays.checked = areAllChecked(dayCheckboxes);
                checkSelections();
            }));

            openScheduleRadios.forEach(radio => radio.addEventListener('change', checkSelections));
            dayOffRadios.forEach(radio => radio.addEventListener('change', checkSelections));
            workIn.addEventListener('input', checkSelections);
            workOff.addEventListener('input', checkSelections);

            form.addEventListener('submit', function(e) {
                // Additional form validation can be added here if needed
            });

            // Initialize page
            checkSelections();

            // Schedule option selection handling
            const fixedScheduleForm = document.getElementById('fixedScheduleForm');
            const tempScheduleForm = document.getElementById('tempScheduleForm');

            function selectOption(optionId) {
                document.querySelectorAll('.schedule-option').forEach(option => {
                    option.classList.remove('selected');
                    option.style.backgroundColor = 'white';
                });

                const selectedOption = document.getElementById(optionId + 'Div');
                selectedOption.classList.add('selected');
                selectedOption.style.backgroundColor = 'limegreen';
                document.getElementById(optionId).checked = true;

                fixedScheduleForm.style.display = optionId === 'fixedSchd' ? 'block' : 'none';
                tempScheduleForm.style.display = optionId === 'tempSchd' ? 'block' : 'none';
                tempDateForm.style.display = optionId === 'tempSchd' ? 'block' : 'none';
            }

            document.getElementById('fixedSchdDiv').addEventListener('click', () => selectOption('fixedSchd'));
            document.getElementById('tempSchdDiv').addEventListener('click', () => selectOption('tempSchd'));

            // Initially hide all forms
            fixedScheduleForm.style.display = 'none';
            tempScheduleForm.style.display = 'none';
            tempDateForm.style.display = 'none';


            // Temporary schedule elements
            const tempForm = document.getElementById('tempScheduleForm');
            const tempCheckAllStaff = document.getElementById('tempCheckAllStaff');
            const tempStaffCheckboxes = tempForm.querySelectorAll('.temp-staff-checkbox');
            const tempScheduleOptions = document.getElementById('tempScheduleOptions');
            const tempWorkIn = tempForm.querySelector('input[name="work_in"]');
            const tempWorkOff = tempForm.querySelector('input[name="work_off"]');
            const tempSubmitButton = document.getElementById('tempSubmitButton');
            const tempDayOffOption = document.getElementById('tempDayOffOption');
            const tempTimeInputs = document.getElementById('tempTimeInputs');

            function handleTempStaffCheckboxChange() {
                const anyStaffChecked = Array.from(tempStaffCheckboxes).some(cb => cb.checked);
                tempScheduleOptions.style.display = anyStaffChecked ? 'block' : 'none';
                checkTempSelections();
            }

            function checkTempSelections() {
                const anyStaffChecked = Array.from(tempStaffCheckboxes).some(cb => cb.checked);
                const openSchedule = tempForm.querySelector('input[name="open_schedule"]:checked').value === '1';
                const dayOff = tempForm.querySelector('input[name="day_off"]:checked').value === '1';

                if (anyStaffChecked) {
                    tempScheduleOptions.style.display = 'block';

                    if (openSchedule) {
                        const dayOffNoOption = tempForm.querySelector('input[name="day_off"][value="0"]');
                        const dayOffYesOption = tempForm.querySelector('input[name="day_off"][value="1"]');
                        dayOffNoOption.checked = true;
                        dayOffYesOption.disabled = dayOffNoOption.disabled = true;
                        tempWorkIn.value = tempWorkOff.value = '';
                        tempDayOffOption.style.display = tempTimeInputs.style.display = 'none';
                        tempSubmitButton.style.display = 'block';
                    } else {
                        const dayOffYesOption = tempForm.querySelector('input[name="day_off"][value="1"]');
                        const dayOffNoOption = tempForm.querySelector('input[name="day_off"][value="0"]');
                        dayOffYesOption.disabled = dayOffNoOption.disabled = false;
                        tempDayOffOption.style.display = 'inline-block';

                        if (dayOff) {
                            tempTimeInputs.style.display = 'none';
                            tempSubmitButton.style.display = 'block';
                            tempWorkIn.value = tempWorkOff.value = '';
                        } else {
                            tempTimeInputs.style.display = 'inline-block';
                            validateTempTimeInputs();
                        }
                    }

                    updateRadioStylingTemp();
                    updateTimeInputStylingTemp();
                } else {
                    tempScheduleOptions.style.display = 'none';
                }
            }

            function validateTempTimeInputs() {
                if (tempWorkIn.value && tempWorkOff.value) {
                    const workInTime = new Date(`1970-01-01T${tempWorkIn.value}:00`);
                    const workOffTime = new Date(`1970-01-01T${tempWorkOff.value}:00`);
                    
                    if (workOffTime <= workInTime) {
                        alert('Work OFF time must be later than Work IN time.');
                        tempSubmitButton.style.display = 'none';
                    } else {
                        tempSubmitButton.style.display = 'block';
                    }
                } else {
                    tempSubmitButton.style.display = 'none';
                }
            }

            function updateRadioStylingTemp() {
                tempForm.querySelectorAll('.custom-radio input[type="radio"]').forEach(radio => {
                    const label = radio.nextElementSibling;
                    if (radio.checked) {
                        label.style.backgroundColor = 'limegreen';
                    } else {
                        label.style.backgroundColor = 'white';
                    }
                });
            }

            function updateTimeInputStylingTemp() {
                tempForm.querySelectorAll('.custom-time input[type="time"]').forEach(input => {
                    input.style.backgroundColor = input.value ? 'limegreen' : 'orange';
                });
            }

            // Event listeners for temporary schedule
            tempCheckAllStaff.addEventListener('change', function() {
                tempStaffCheckboxes.forEach(cb => cb.checked = this.checked);
                handleTempStaffCheckboxChange();
            });

            tempStaffCheckboxes.forEach(cb => cb.addEventListener('change', handleTempStaffCheckboxChange));

            tempForm.querySelectorAll('input[name="open_schedule"]').forEach(radio => radio.addEventListener('change', checkTempSelections));
            tempForm.querySelectorAll('input[name="day_off"]').forEach(radio => radio.addEventListener('change', checkTempSelections));
            tempWorkIn.addEventListener('input', checkTempSelections);
            tempWorkOff.addEventListener('input', checkTempSelections);

            // Initialize temporary schedule options
            handleTempStaffCheckboxChange();
        });


    </script>


</body>

</html>