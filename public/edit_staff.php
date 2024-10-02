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
$titles = $user->getAllTitles();

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    redirectTo('manage_staff.php');
}

$staffId = $_GET['id'];
$staffMember = $user->getStaffById($staffId);

if (!$staffMember) {
    redirectTo('manage_staff.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $staffData = [
        'staff_id' => $staffId,
        'title_id' => filter_input(INPUT_POST, 'title_id', FILTER_SANITIZE_NUMBER_INT),
        'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
        'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
        'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT),
        'attendance_req' => filter_input(INPUT_POST, 'attendance_req', FILTER_SANITIZE_NUMBER_INT),
        'joining_date' => filter_input(INPUT_POST, 'joining_date', FILTER_SANITIZE_STRING),
        'termination_date' => $_POST['termination_date'] ? filter_input(INPUT_POST, 'termination_date', FILTER_SANITIZE_STRING) : null,
        'phone_number' => filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING),
        'email_address' => filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL)
    ];

    $result = $user->editStaff($staffData);
    if ($result === true) {
        $success = "Staff member updated successfully.";
        $staffMember = $user->getStaffById($staffId); // Refresh data
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff Member - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Edit Staff Member</h1>
            <a href="manage_staff.php" class="btn btn-primary">Back to Manage Staff</a>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
        </header>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="edit_staff.php?id=<?php echo $staffId; ?>" method="post">
            <div class="input-group">
                <label for="title_id">Title:</label>
                <select name="title_id" id="title_id" required>
                    <?php foreach ($titles as $title): ?>
                        <option value="<?php echo $title['title_id']; ?>" <?php echo ($title['title_id'] == $staffMember['title_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($title['title_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($staffMember['first_name']); ?>" required>
            </div>
            <div class="input-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($staffMember['last_name']); ?>" required>
            </div>
            <div class="input-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="0" <?php echo $staffMember['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                    <option value="1" <?php echo $staffMember['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                </select>
            </div>
            <div class="input-group">
                <label for="attendance_req">Attendance Required:</label>
                <select name="attendance_req" id="attendance_req" required>
                    <option value="0" <?php echo $staffMember['attendance_req'] == 0 ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo $staffMember['attendance_req'] == 1 ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
            <div class="input-group">
                <label for="joining_date">Joining Date:</label>
                <input type="date" id="joining_date" name="joining_date" value="<?php echo $staffMember['joining_date']; ?>" required>
            </div>
            <div class="input-group">
                <label for="termination_date">Termination Date:</label>
                <input type="date" id="termination_date" name="termination_date" value="<?php echo $staffMember['termination_date'] ?? ''; ?>">
            </div>
            <div class="input-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($staffMember['phone_number'] ?? ''); ?>">
            </div>
            <div class="input-group">
                <label for="email_address">Email Address:</label>
                <input type="email" id="email_address" name="email_address" value="<?php echo htmlspecialchars($staffMember['email_address'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Staff Member</button>
        </form>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>