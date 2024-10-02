<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/auth.php';
requireLogin();

if (!isAdmin()) {
    redirectTo('dashboard.php');
}

$user = new User(getDbConnection());
$staffMembers = $user->getAllStaff();
$titles = $user->getAllTitles();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_staff'])) {
        // Handle add staff form submission
        $result = $user->addStaff($_POST);
        if ($result === true) {
            $success = "Staff member added successfully.";
        } else {
            $error = $result;
        }
    } elseif (isset($_POST['edit_staff'])) {
        // Handle edit staff form submission
        $result = $user->editStaff($_POST);
        if ($result === true) {
            $success = "Staff member updated successfully.";
        } else {
            $error = $result;
        }
    }
    // Refresh staff list after add/edit
    $staffMembers = $user->getAllStaff();
}

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
            <h1>Manage Staff</h1>
            <a href="admin.php" class="btn btn-primary">Back to Admin Dashboard</a>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
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
                <label for="title_id">Title:</label>
                <select name="title_id" id="title_id" required>
                    <?php foreach ($titles as $title): ?>
                        <option value="<?php echo $title['title_id']; ?>"><?php echo htmlspecialchars($title['title_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="input-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="input-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="0">Inactive</option>
                    <option value="1">Active</option>
                </select>
            </div>
            <div class="input-group">
                <label for="attendance_req">Attendance Required:</label>
                <select name="attendance_req" id="attendance_req" required>
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div class="input-group">
                <label for="joining_date">Joining Date:</label>
                <input type="date" id="joining_date" name="joining_date" required>
            </div>
            <div class="input-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number">
            </div>
            <div class="input-group">
                <label for="email_address">Email Address:</label>
                <input type="email" id="email_address" name="email_address">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffMembers as $staff): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['title_name']); ?></td>
                        <td><?php echo $staff['status'] ? 'Active' : 'Inactive'; ?></td>
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