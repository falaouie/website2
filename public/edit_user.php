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
$error = '';
$success = '';

if (!isset($_GET['id'])) {
    redirectTo('manage_users.php');
}

$userId = $_GET['id'];
$userData = $user->getUserById($userId);

if (!$userData) {
    redirectTo('manage_users.php');
}

$staffMembers = $user->getAllStaff();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_user'])) {
        $updateData = [
            'id' => $userId,
            'username' => $_POST['username'],
            'staff_id' => $_POST['staff_id'] ? $_POST['staff_id'] : null,
        ];

        $result = $user->editUser($updateData);
        if ($result === true) {
            $success = "User updated successfully.";
            $userData = $user->getUserById($userId); // Refresh data
        } else {
            $error = $result;
        }
    } elseif (isset($_POST['reset_password'])) {
        $result = $user->resetPassword($userId);
        if ($result === true) {
            $success = "Password reset successfully.";
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Edit User</h1>
            <a href="manage_users.php" class="btn btn-primary">Back to Manage Users</a>
            <a href="logout.php" class="btn btn-danger">LOGOUT</a>
        </header>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="edit_user.php?id=<?php echo $userId; ?>" method="post" class="edit-user-form">
            <input type="hidden" name="update_user" value="1">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
            </div>
            <div class="input-group">
                <label for="staff_id">Associated Staff:</label>
                <select name="staff_id" id="staff_id">
                    <option value="">None (Admin)</option>
                    <?php foreach ($staffMembers as $staff): ?>
                        <option value="<?php echo $staff['staff_id']; ?>" <?php echo ($staff['staff_id'] == $userData['staff_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>

        <form action="edit_user.php?id=<?php echo $userId; ?>" method="post" class="reset-password-form">
            <input type="hidden" name="reset_password" value="1">
            <button type="submit" class="btn btn-warning">Reset Password</button>
        </form>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>