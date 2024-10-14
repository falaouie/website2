<?php

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();

// At the top of your PHP script
$selectedDate = $_POST['temp_date'] ?? date('Y-m-d');

function formatTime($time) {
    if (empty($time)) return '';
    $timestamp = strtotime($time);
    return date('h:i A', $timestamp);
}

$user = new User(getDbConnection());

$staffWithAttendance = $user->getStaffWithAttendanceRequired();

$currentSchedules = $user->getCurrentFixedSchedules();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_temp_schedule'])) {
        $result = $user->updateTemporarySchedule($_POST);
        $message = $result === true ? "Temporary schedules updated successfully." : "Error: " . $result;
    } elseif (isset($_POST['update_fixed_schedule'])) {
        $result = $user->updateFixedSchedule($_POST);
        $message = $result === true ? "Fixed schedules updated successfully." : "Error: " . $result;
        $currentSchedules = $user->getCurrentFixedSchedules(); // Refresh schedules after update
    }
}


$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

$tempSchedules = $user->getTemporarySchedules($selectedDate);
$fixedSchedules = $user->getFixedSchedulesForDay($selectedDate);
$reasons = $user->getReasons();

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


    <form id="tempScheduleForm" method="post" action="">
        <div>
            <label for="tempDate">Select Date:</label>
            <input type="date" id="tempDate" name="temp_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
            <button type="submit" id="tempLoadScheduleButton" style="display: none;">Load Schedule</button>
        </div>
        
        <table class="schedule-table">
            <thead>
                <tr>
                    <th colspan="7" style="text-align: center;">
                        <?php echo date('l, d/m/Y', strtotime($selectedDate)); ?>
                    </th>
                </tr>
                <tr>
                    <th>Staff Name</th>
                    <th><input type="checkbox" id="checkAllStaffTemp"> All</th>
                    <th>From Time</th>
                    <th>To Time</th>
                    <th>Reason</th>
                    <th colspan="2">Fixed Schedule</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user->getStaffWithAttendanceRequired() as $staff) :
                    $tempSchedule = $tempSchedules[$staff['staff_id']] ?? null;
                    $fixedSchedule = $fixedSchedules[$staff['staff_id']] ?? null;
                    $scheduleText = '';
                    if ($tempSchedule) {
                        if ($tempSchedule['day_off'] == 1) {
                            $scheduleText = 'DAY OFF';
                        } elseif ($tempSchedule['open_schedule'] == 1) {
                            $scheduleText = 'OPEN';
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></td>
                        <td><input type="checkbox" name="temp_staff[]" value="<?php echo $staff['staff_id']; ?>"></td>
                        <td><?php echo $scheduleText ?: ($tempSchedule ? htmlspecialchars($tempSchedule['scheduled_in']) : ''); ?></td>
                        <td><?php echo $scheduleText ?: ($tempSchedule ? htmlspecialchars($tempSchedule['scheduled_out']) : ''); ?></td>
                        <td><?php echo $tempSchedule ? htmlspecialchars($reasons[$tempSchedule['reason_id']]['text'] ?? '') : ''; ?></td>
                        <td><?php echo $fixedSchedule['start_time'] ?? 'N/A'; ?></td>
                        <td><?php echo $fixedSchedule['end_time'] ?? 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="tempScheduleOptions" class="hidden">
            <div class="radio-group">
                <span class="radio-group-label">Open Schedule:</span>
                <label class="custom-radio">
                    <input type="radio" name="temp_open_schedule" value="0" checked>
                    <span class="radio-label">No</span>
                </label>
                <label class="custom-radio">
                    <input type="radio" name="temp_open_schedule" value="1">
                    <span class="radio-label">Yes</span>
                </label>
            </div>
            <div id="tempDayOffOption" class="radio-group">
                <span class="radio-group-label">Day OFF:</span>
                <label class="custom-radio">
                    <input type="radio" name="temp_day_off" value="0" checked>
                    <span class="radio-label">No</span>
                </label>
                <label class="custom-radio">
                    <input type="radio" name="temp_day_off" value="1">
                    <span class="radio-label">Yes</span>
                </label>
            </div>
            <div id="tempTimeInputs">
                <label class="custom-time">
                    Work IN: <input type="time" name="temp_work_in" placeholder=" ">
                </label>
                <label class="custom-time">
                    Work OFF: <input type="time" name="temp_work_off" placeholder=" ">
                </label>
            </div>
            <div>
                <label for="temp_reason">Reason:</label>
                <select name="temp_reason" id="temp_reason">
                    <option value="">Select Reason</option>
                    <?php foreach ($reasons as $reason) : ?>
                        <option value="<?php echo $reason['id']; ?>"><?php echo htmlspecialchars($reason['text']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update_temp_schedule" id="tempSubmitButton" class="btn btn-primary">Update Temporary Schedules</button>
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
            }

            document.getElementById('fixedSchdDiv').addEventListener('click', () => selectOption('fixedSchd'));
            document.getElementById('tempSchdDiv').addEventListener('click', () => selectOption('tempSchd'));

            // Initially hide both forms
            fixedScheduleForm.style.display = 'none';
            tempScheduleForm.style.display = 'none';

            // Temporary Schedule Form
            const tempDateInput = document.getElementById('tempDate');
            const tempLoadScheduleButton = document.getElementById('tempLoadScheduleButton');
            const tempForm = document.getElementById('tempScheduleForm');
            const tempScheduleOptions = document.getElementById('tempScheduleOptions');
            const checkAllStaffTemp = document.getElementById('checkAllStaffTemp');
            const tempStaffCheckboxes = document.querySelectorAll('input[name="temp_staff[]"]');
            const tempOpenScheduleRadios = document.querySelectorAll('input[name="temp_open_schedule"]');
            const tempDayOffRadios = document.querySelectorAll('input[name="temp_day_off"]');
            const tempTimeInputs = document.getElementById('tempTimeInputs');
            const tempWorkIn = document.querySelector('input[name="temp_work_in"]');
            const tempWorkOff = document.querySelector('input[name="temp_work_off"]');
            const tempSubmitButton = document.getElementById('tempSubmitButton');

            tempDateInput.addEventListener('change', function() {
                tempLoadScheduleButton.style.display = 'inline-block';
            });

            checkAllStaffTemp.addEventListener('change', function() {
                tempStaffCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleTempScheduleOptions();
            });

            tempStaffCheckboxes.forEach(cb => cb.addEventListener('change', function() {
                checkAllStaffTemp.checked = Array.from(tempStaffCheckboxes).every(cb => cb.checked);
                toggleTempScheduleOptions();
            }));

            function toggleTempScheduleOptions() {
                const checkedStaff = document.querySelectorAll('input[name="temp_staff[]"]:checked');
                tempScheduleOptions.classList.toggle('hidden', checkedStaff.length === 0);
            }

            tempOpenScheduleRadios.forEach(radio => radio.addEventListener('change', updateTempScheduleOptions));
            tempDayOffRadios.forEach(radio => radio.addEventListener('change', updateTempScheduleOptions));

            function updateTempScheduleOptions() {
                const isOpenSchedule = document.querySelector('input[name="temp_open_schedule"]:checked').value === '1';
                const isDayOff = document.querySelector('input[name="temp_day_off"]:checked').value === '1';

                tempTimeInputs.classList.toggle('hidden', isOpenSchedule || isDayOff);
                document.getElementById('tempDayOffOption').classList.toggle('hidden', isOpenSchedule);
            }

            [tempWorkIn, tempWorkOff].forEach(input => {
                input.addEventListener('change', function() {
                    if (tempWorkIn.value && tempWorkOff.value && tempWorkIn.value >= tempWorkOff.value) {
                        alert('Work OFF time must be later than Work IN time.');
                        this.value = '';
                    }
                    validateTempTimeInputs();
                });
            });

            function validateTempTimeInputs() {
                const isOpenSchedule = document.querySelector('input[name="temp_open_schedule"]:checked').value === '1';
                const isDayOff = document.querySelector('input[name="temp_day_off"]:checked').value === '1';

                if (!isOpenSchedule && !isDayOff) {
                    tempSubmitButton.disabled = !(tempWorkIn.value && tempWorkOff.value);
                } else {
                    tempSubmitButton.disabled = false;
                }
            }

            tempForm.addEventListener('submit', function(e) {
                if (e.submitter && e.submitter.name === 'update_temp_schedule') {
                    const checkedStaff = document.querySelectorAll('input[name="temp_staff[]"]:checked');
                    if (checkedStaff.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one staff member to update the schedule.');
                    }
                }
            });

            // Initialize options
            updateTempScheduleOptions();
            validateTempTimeInputs();
        });

        // Function to select TEMPORARY tab on page load
        function selectTemporaryTab() {
            document.querySelector('input[value="TEMPORARY"]').checked = true;
        }

        // Call the function when the page loads
        window.onload = selectTemporaryTab;
    </script>


</body>

</html>