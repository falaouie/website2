<?php

date_default_timezone_set('Asia/Beirut');

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();



$user = new User(getDbConnection());

$firstName = $_SESSION['first_name'];

$today = date('Y-m-d'); // Current date in Y-m-d format
$todayFormatted = date('d/m/Y', strtotime($today)); // Convert to dd/mm/yyyy format

$staffList = [];
$attendanceList = [];

if (isset($_SESSION['fromDate'])) {
    $fromDate = $_SESSION['fromDate'];
}
if (isset($_SESSION['toDate'])) {
    $toDate = $_SESSION['toDate'];
}

if (!isset($_SESSION['fromDate'])) {
    $_SESSION['fromDate'] = $today;
    $fromDate = $_SESSION['fromDate'];
}
if (!isset($_SESSION['toDate'])) {
    $_SESSION['toDate'] = $today;
    $toDate = $_SESSION['toDate'];
}



if (!isset($_SESSION['staffStatus'])) {
    $_SESSION['staffStatus'] = 1;
    // Default to Active Staff if no status is selected
    $staffList = $user->getActiveStaff();
}

if (isset($_SESSION['staffID'])) {
    $staffID = $_SESSION['staffID'];
}
if (!isset($_SESSION['staffID'])) {
    $_SESSION['staffID'] = 'allStaff';
    $staffID = $_SESSION['staffID'];
}

// Check if the date form was submitted and store the values in sessions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['fromDate'])) {
        $_SESSION['fromDate'] = $_POST['fromDate'];
        $fromDate = $_SESSION['fromDate'];
    }
    if (isset($_POST['toDate'])) {
        $_SESSION['toDate'] = $_POST['toDate'];
        $toDate = $_SESSION['toDate'];
    }
    if (isset($_POST['staffID'])) {
        $_SESSION['staffID'] = $_POST['staffID'];
        $staffID = $_SESSION['staffID'];
    }


    // echo 'staff id '.$staffID.'<br>';
    // echo 'from date '.$fromDate.'<br>';
    // echo 'to date '.$toDate.'<br>';
}


// Check if a staff status has been selected
if (isset($_POST['staffStatus'])) {
    $staffStatus = $_POST['staffStatus'];
    $_SESSION['staffStatus'] = $_POST['staffStatus'];
    unset($_SESSION['staffID']);
}
$staffStatus = $_SESSION['staffStatus'];
// Get the staff list based on the selected status
if ($staffStatus === '1') {
    // Active Staff
    $staffList = $user->getActiveStaff();
} elseif ($staffStatus === '0') {
    // Inactive Staff
    $staffList = $user->getInactiveStaff();
} elseif ($staffStatus === 'allStaff') {
    // All Staff
    $staffList = $user->getAllStaff();
}

$fromDateFormatted = date('d/m/Y', strtotime($fromDate));
$toDateFormatted = date('d/m/Y', strtotime($toDate));

if ($staffID && $fromDate && $toDate) {
    $attendanceList = $user->getAttendanceList($staffID, $fromDate, $toDate);
}

// var_dump($attendanceList);
// exit;
// echo 'staff '.$staffID;
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staff Attendance - Silver System</title>

    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .div-group {
            display: flex;
            align-items: center; /* Vertically centers the content */
            justify-content: center; /* Horizontally centers the content */
            margin-bottom: 10px;
            width: 100%; /* Ensure the container takes up full width (if needed) */
        }

        .div-item {
            display: flex;
            align-items: center; /* Vertically centers the content */
            justify-content: center; /* Horizontally centers the content */
            margin-left: 10px;
        }

        /* Style for the schedule options */
        .schedule-option {
            padding: 5px 5px;
            outline: 1px solid black;
            margin-left: 10px;
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

        label {
            margin-right: 10px; /* Adjust the spacing as needed */
        }

        /* Adjust the width of the select element */
        #select_staff {
            width: auto;                /* Automatically adjust width */
            min-width: 20ch;            /* Minimum width to avoid being too narrow */
            max-width: 100%;            /* Maximum width to ensure it doesn't overflow the container */
            padding: 5px;               /* Add some padding for better appearance */
            margin-left: 10px;
        }

    </style>

</head>

<body>

    <div class="dashboard-container">

      <header class="screen-only">

        <div>

            <a href="../dashboard.php" class="btn btn-primary">DASHBOARD</a>

            <a href="attendance.php" class="btn btn-primary">ATTENDANCE</a>

        </div>

        

        <div>

            <a href="../logout.php" class="btn btn-danger">LOGOUT</a>

        </div>

      </header>

        <div>

            <h1 class="dashboard-title">Attendance History</h1>

        </div>

        <div class="dashboard-container screen-only">
        
            


            <form id="staffStatusSelection" action="" method="post">
                <div class="div-group">
                    <div class="div-item">
                        Staff Status
                    </div>
                    <div id="activeStatusDiv" class="schedule-option" style="background-color: <?php echo (isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '1') ? 'limegreen' : 'white'; ?>">
                        <strong><label for="activeStatus">Active</label></strong>
                        <input class="screen-only" id="activeStatus" type="radio" name="staffStatus" value="1" 
                        <?php echo (isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '1') ? 'checked' : ''; ?>
                        onchange="this.form.submit();">
                    </div>
                    <div id="inactiveStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '0' ? 'limegreen' : 'white'; ?>">
                        <strong><label for="inactiveStatus">In-Active</label></strong>
                        <input class="screen-only" id="inactiveStatus" type="radio" name="staffStatus" value="0" 
                        <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '0' ? 'checked' : ''; ?>
                        onchange="this.form.submit();">
                    </div>
                    <div id="allStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == 'allStaff' ? 'limegreen' : 'white'; ?>">
                        <strong><label for="allStatus">All</label></strong>
                        <input class="screen-only" id="allStatus" type="radio" name="staffStatus" value="allStaff" 
                        <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == 'allStaff' ? 'checked' : ''; ?>
                        onchange="this.form.submit();">
                    </div>
                </div>
            </form>


            <!-- Attendance History Form -->
            <form id="attendanceHistForm" method="post" action="">
                <div class="div-group">
                    <div class="div-item">
                        <label for="fromDate">From</label>
                        <input type="date" name="fromDate" id="fromDate" value="<?php echo isset($_SESSION['fromDate']) ? $fromDate : $today; ?>" required>
                    </div>
                    <div class="div-item">
                        <label for="toDate">To</label>
                        <input type="date" name="toDate" id="toDate" value="<?php echo isset($_SESSION['toDate']) ? $toDate : $today; ?>" required>
                    </div>
                </div>

                <div class="div-group">
                    
                    <div class="div-item">
                        <!-- Visible input field with staff names -->
                        <input id="select_staff" list="selectStaff" placeholder="Select Staff Name" autocomplete="off" autofocus="true" required>
                        
                        <!-- Hidden field to store staff_id -->
                        <input type="hidden" id="selected_staff_id" name="staffID">
                        
                        <!-- Datalist with staff names as visible options -->
                        <datalist id="selectStaff">
                            <option value="All Staff">All Staff</option> 
                            <?php foreach ($staffList as $staff) : ?>
                                <option data-id="<?php echo $staff['staff_id']; ?>" value="<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>">
                                    <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="div-item">
                        <button type="submit" id="submitAttendHistForm" name="submitAttendHistForm" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>

        </div>
        

        <div class="dashboard-container">
            <?php
                if ($staffID == 'allStaff' && $attendanceList) {
                    if ($fromDate == $toDate) {
                        ?>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <div class="div-group">
                                            <div class="div-item">
                                                All Staff
                                            </div>
                                            <div class="div-item">
                                                &nbsp;
                                            </div>
                                            <div class="div-item greenText">
                                                <?php
                                                if ($fromDate == $toDate) {
                                                    echo date('l', strtotime($fromDate));
                                                }
                                                ?>
                                            </div>
                                            <div class="div-item">
                                                &nbsp;
                                            </div>
                                            <div class="div-item greenText">
                                                    <?php
                                                    if (isset($_SESSION['fromDate'])) {
                                                        echo $fromDateFormatted;
                                                    } else {
                                                        echo $todayFormatted;
                                                    }
                                                    ?>
                                            </div>
                                            <div class="div-item">
                                                To
                                            </div>
                                            <div class="div-item greenText">
                                                <?php
                                                    if (isset($_SESSION['toDate'])) {
                                                        echo $toDateFormatted;
                                                    } else {
                                                        echo $todayFormatted;
                                                    }
                                                    ?>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                       <div class="div-group">
                                            <button class="btn btn-print" onclick="window.print()">Print</button>
                                       </div>                     
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Name         
                                    </th>
                                    <th>
                                        Day        
                                    </th>
                                    <th>
                                        Work In
                                    </th>
                                    <th>
                                        Work Off    
                                    </th>
                                    <th>
                                        Hours
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($attendanceList) {
                                    $totalHours = 0;
                                    foreach ($attendanceList as $row) {
                                        if ($row['hours_worked']) {
                                            ?>
                                            <tr>
                                                <td>
                                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                                </td>
                                                <td>
                                                <?php echo date('l', strtotime($row['work_date'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars(formatTime($row['work_in'])); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        if ($row['work_off']) {
                                                            echo htmlspecialchars(formatTime($row['work_off']));
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $hoursWorked = $row['hours_worked'];
                                                        $totalHours = $totalHours + $hoursWorked;
                                                        echo $hoursWorked;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td class="redText" colspan="5">
                                            <div class="div-group">
                                                <strong>No Records Found</strong>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                
                                ?>
                            </tbody>
                        </table>
                        <?php 
                    } else {
                        echo 'list each staff with date range or list dates with attendance staff ???';
                    }
                    
                } elseif ($staffID != 'allStaff' && $attendanceList) {
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="4">
                                    <div class="div-group">
                                        <div class="div-item">
                                            <?php if ($attendanceList && !empty($attendanceList)) : ?>
                                                <?php echo htmlspecialchars($attendanceList[0]['first_name'] . ' ' . $attendanceList[0]['last_name']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="div-item">
                                            &nbsp;
                                        </div>
                                        <div class="div-item greenText">
                                                <?php
                                                if (isset($_SESSION['fromDate'])) {
                                                    echo $fromDateFormatted;
                                                } else {
                                                    echo $todayFormatted;
                                                }
                                                ?>
                                        </div>
                                        <div class="div-item">
                                            To
                                        </div>
                                        <div class="div-item greenText">
                                            <?php
                                                if (isset($_SESSION['toDate'])) {
                                                    echo $toDateFormatted;
                                                } else {
                                                    echo $todayFormatted;
                                                }
                                                ?>
                                        </div>
                                    </div> 
                                </th>
                                <th>
                                   <div class="div-group">
                                        <button class="btn btn-print screen-only" onclick="window.print()">Print</button>
                                   </div>                     
                                </th>
                            </tr>
                            <tr>
                                <th>
                                Date         
                                </th>
                                <th>
                                    Day        
                                </th>
                                <th>
                                    Work In
                                </th>
                                <th>
                                    Work Off    
                                </th>
                                <th>
                                    Hours
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if ($attendanceList) {
                                    $totalHours = 0;
                                    foreach ($attendanceList as $row) {
                                        if ($row['hours_worked']) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($row['work_date'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo date('l', strtotime($row['work_date'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo date('H:i', strtotime($row['work_in'])); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        if ($row['work_off']) {
                                                            echo date('H:i', strtotime($row['work_off'])); 
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $hoursWorked = $row['hours_worked'];
                                                        $totalHours = $totalHours + $hoursWorked;
                                                        echo $hoursWorked;
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                        <tr>
                                            <td colspan="4">
                                                &nbsp;
                                            </td>
                                            <td>
                                                <?php
                                                    echo $totalHours;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                } else {
                                    ?>
                                    <tr>
                                        <td class="redText" colspan="5">
                                            <div class="div-group">
                                                <strong>No Records Found</strong>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                
                                ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    ?>
                        <div class="div-group redText">
                            No Records Found
                        </div>
                    <?php
                }
                
            ?>
            
        </div>

    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        // Get the input elements
        const selectStaffInput = document.getElementById('select_staff');
        const hiddenStaffIdInput = document.getElementById('selected_staff_id');
        const dataList = document.getElementById('selectStaff');

        // Event listener for when the user selects a staff from the datalist
        selectStaffInput.addEventListener('input', function() {
            const inputValue = selectStaffInput.value;

            // Reset hidden input
            hiddenStaffIdInput.value = '';

            // Check if the selected value matches any option in the datalist
            if (inputValue === "All Staff") {
                // Set the hidden field to "All" when "All Staff" is selected
                hiddenStaffIdInput.value = "allStaff";
            } else {
                // Loop through options and find the matching staff name
                for (let i = 0; i < dataList.options.length; i++) {
                    if (dataList.options[i].value === inputValue) {
                        hiddenStaffIdInput.value = dataList.options[i].getAttribute('data-id');
                        break;
                    }
                }
            }
        });

        // Date inputs code
        document.getElementById('attendanceHistForm').addEventListener('submit', function(event) {
            // Get the date values from the inputs
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;

            // Check if both dates are complete
            if (!fromDate || !toDate) {
                alert('Both From and To dates must be filled.');
                event.preventDefault(); // Prevent form submission
                return;
            }

            // Convert the date strings to Date objects for comparison
            const fromDateObj = new Date(fromDate);
            const toDateObj = new Date(toDate);

            // Check if fromDate is greater than toDate
            if (fromDateObj > toDateObj) {
                alert("'From' date must be less than or equal to 'To' date.");
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>


</body>


</html>