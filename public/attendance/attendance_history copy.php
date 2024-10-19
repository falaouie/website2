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

$staffList = [];

// Check if a staff status has been selected
if (isset($_POST['staffStatus'])) {
    $staffStatus = $_POST['staffStatus'];
    
    // Get the staff list based on the selected status
    if ($staffStatus === '1') {
        // Active Staff
        $staffList = $user->getActiveStaff();
    } elseif ($staffStatus === '0') {
        // Inactive Staff
        $staffList = $user->getInactiveStaff();
    } elseif ($staffStatus === 'all') {
        // All Staff
        $staffList = $user->getAllStaff();
    }
} else {
    // Default to Active Staff if no status is selected
    $staffList = $user->getActiveStaff();
}


// function getFormValue($field) {

//     return isset($_SESSION['form_data'][$field]) ? htmlspecialchars($_SESSION['form_data'][$field]) : '';

// }

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

            <table>
                <thead>

                    <tr>
                        <th>
                            <div class="div-group">
                                <div class="div-item">
                                    <label for="fromDate">From</label>
                                    <input type="date" name="fromDate" id="fromDate" required>
                                </div>
                                <div class="div-item">
                                    <label for="toDate">To</label>
                                    <input type="date" name="toDate" id="toDate" required>
                                </div>
                            </div>
                        </th>
                    </tr>

                    <tr>

                        <th>

                            <form id="staffStatusSelection" action="" method="post">
                                <div class="div-group">
                                    <div class="div-item">
                                        Staff Status
                                    </div>
                                    <div id="activeStatusDiv" class="schedule-option" style="background-color: <?php echo (isset($_POST['staffStatus']) && $_POST['staffStatus'] == '1') || !isset($_POST['staffStatus']) ? 'limegreen' : 'white'; ?>">
                                        <strong><label for="activeStatus">Active</label></strong>
                                        <input class="screen-only" id="activeStatus" type="radio" name="staffStatus" value="1" 
                                        <?php echo (isset($_POST['staffStatus']) && $_POST['staffStatus'] == '1') || !isset($_POST['staffStatus']) ? 'checked' : ''; ?>
                                        onchange="this.form.submit();">
                                    </div>
                                    <div id="inactiveStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_POST['staffStatus']) && $_POST['staffStatus'] == '0' ? 'limegreen' : 'white'; ?>">
                                        <strong><label for="inactiveStatus">In-Active</label></strong>
                                        <input class="screen-only" id="inactiveStatus" type="radio" name="staffStatus" value="0" 
                                        <?php echo isset($_POST['staffStatus']) && $_POST['staffStatus'] == '0' ? 'checked' : ''; ?>
                                        onchange="this.form.submit();">
                                    </div>
                                    <div id="allStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_POST['staffStatus']) && $_POST['staffStatus'] == 'all' ? 'limegreen' : 'white'; ?>">
                                        <strong><label for="allStatus">All</label></strong>
                                        <input class="screen-only" id="allStatus" type="radio" name="staffStatus" value="all" 
                                        <?php echo isset($_POST['staffStatus']) && $_POST['staffStatus'] == 'all' ? 'checked' : ''; ?>
                                        onchange="this.form.submit();">
                                    </div>
                                </div>
                            </form>

                            
                        </th>

                    </tr>

                    <tr>
                            <th>
                                <!-- Attendance History Form -->
                                <form id="attendanceHistForm" method="post" action="">
                                    <div class="div-group">
                                        
                                        <div class="div-item">
                                            <!-- Visible input field with staff names -->
                                            <input id="select_staff" list="selectStaff" placeholder="Select Staff Name" autocomplete="off" autofocus="true" required>
                                            
                                            <!-- Hidden field to store staff_id -->
                                            <input type="hidden" id="selected_staff_id" name="select_staff_id">
                                            
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
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </form>

                            </th>
                        </tr>
                </thead>
                                         
            </table>
        </div>

    </div>

    <!-- <script src="../assets/js/app.js"></script> -->
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
    </script>

</body>


</html>