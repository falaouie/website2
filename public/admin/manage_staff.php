<?php
session_start();
require_once '../../includes/functions.php';
require_once '../../includes/User.php';
require_once '../../includes/auth.php';
requireLogin();

$user = new User(getDbConnection());
$staffMembers = $user->getAllStaff();
$titles = $user->getAllTitles();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_staff'])) {
        // Store form data in session
        $_SESSION['form_data'] = $_POST;
        
        $result = $user->addStaff($_POST);
        if ($result === true) {
            $success = "Staff member added successfully.";
            // Clear session data on success
            unset($_SESSION['form_data']);
        } else {
            $error = $result;
        }
    }
    // Refresh staff list after add/edit
    $staffMembers = $user->getAllStaff();
}

// Function to get form value from session or empty string
function getFormValue($field) {
        return isset($_SESSION['form_data'][$field]) ? htmlspecialchars($_SESSION['form_data'][$field]) : '';
    }

    // Sort titles by title_id
    usort($titles, function ($a, $b) {
        return $a['title_id'] <=> $b['title_id'];
    });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div><h1>Manage Staff</h1></div>
            <div><a href="admin.php" class="btn btn-primary">Admin Dashboard</a></div>
            <div><a href="../logout.php" class="btn btn-danger">LOGOUT</a></div>
        </header>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <h2>Add New Staff Member</h2>
        <form action="manage_staff.php" method="post">
            <input type="hidden" name="add_staff" value="1">
            <div class="input-group">
                <label for="title_id">Job Title:</label>
                <select name="title_id" id="title_id" required>
                    <option value="">Select</option>
                    <?php foreach ($titles as $title): ?>
                        <option value="<?php echo $title['title_id']; ?>" <?php echo getFormValue('title_id') == $title['title_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($title['title_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo getFormValue('first_name'); ?>" required>
            </div>
            <div class="input-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo getFormValue('last_name'); ?>" required>
            </div>
            <div class="input-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="">Select</option>
                    <option value="0" <?php echo getFormValue('status') == '0' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="1" <?php echo getFormValue('status') == '1' ? 'selected' : ''; ?>>Active</option>
                </select>
            </div>
            <div class="input-group">
                <label for="attendance_req">Attendance Required:</label>
                <select name="attendance_req" id="attendance_req" required>
                    <option value="">Select</option>
                    <option value="0" <?php echo getFormValue('attendance_req') == '0' ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo getFormValue('attendance_req') == '1' ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="input-group">
                <label for="joining_date">Joining Date:</label>
                <input type="date" id="joining_date" name="joining_date" value="<?php echo getFormValue('joining_date'); ?>" required>
            </div>
            <div class="input-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo getFormValue('phone_number'); ?>" required>
            </div>
            <div class="input-group">
                <label for="email_address">Email Address:</label>
                <input type="email" id="email_address" name="email_address" value="<?php echo getFormValue('email_address'); ?>">
            </div>
            <div class="input-group">
                <label for="system_access">System Access:</label>
                <select name="system_access" id="system_access" required>
                    <option value="">Select</option>
                    <option value="0" <?php echo getFormValue('system_access') == '0' ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo getFormValue('system_access') == '1' ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Staff Member</button>
        </form>
        <h2>Current Staff Members</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>System Access</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffMembers as $staff): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['title_name']); ?></td>
                        <td><?php echo $staff['status'] ? 'Active' : 'Inactive'; ?></td>
                        <td><?php echo $staff['system_access'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <a href="edit_staff.php?id=<?php echo $staff['staff_id']; ?>" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
</table>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>