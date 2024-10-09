<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/User.php';
require_once '../includes/auth.php';
requireLogin();

$user = new User(getDbConnection());
$users = $user->getAllUsers();
$availableStaff = $user->getStaffWithoutUsers();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $_SESSION['form_data'] = $_POST;
        
        $result = $user->addUser($_POST);
        if ($result === true) {
            $success = "User added successfully.";
            unset($_SESSION['form_data']);
        } else {
            $error = $result;
        }
    } elseif (isset($_POST['reset_password'])) {
        $userId = $_POST['user_id'];
        $result = $user->resetPassword($userId);
        if ($result === true) {
            $success = "Password reset successfully for user ID: " . $userId;
        } else {
            $error = $result;
        }
    }
    $users = $user->getAllUsers();
    $availableStaff = $user->getStaffWithoutUsers();
}

function getFormValue($field) {
    return isset($_SESSION['form_data'][$field]) ? htmlspecialchars($_SESSION['form_data'][$field]) : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Silver System</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div><h1>Manage Users</h1></div>
            <div><a href="admin.php" class="btn btn-primary btn-medium">Admin Dashboard</a></div>
            <div><a href="logout.php" class="btn btn-danger btn-medium">LOGOUT</a></div>
        </header>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <section class="users-section">
            <h2>Add New User <span class="text-color-danger">(Only for staff that have system access)</span></h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Staff Name</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <form action="manage_users.php" method="post" class="add-user-form" id="addUserForm">
                            <input type="hidden" name="add_user" value="1">
                            <td><input type="text" id="username" name="username" required maxlength="30" class="wide-input" value="<?php echo getFormValue('username'); ?>" autofocus></td>
                            <td>
                                <select name="staff_id" id="staff_id" required>
                                    <option value="">Select</option>
                                    <?php foreach ($availableStaff as $staff): ?>
                                        <option value="<?php echo $staff['staff_id']; ?>" <?php echo getFormValue('staff_id') == $staff['staff_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><button type="submit" class="btn btn-primary btn-medium">Add User</button></td>
                        </form>
                    </tr>
                </tbody>
            </table>

            <h2>Current Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Staff Name</th>
                        <th>Password</th>
                        <th>Access</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $currentUser): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($currentUser['username']); ?></td>
                            <td><?php echo $currentUser['staff_id'] ? htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) : 'Admin'; ?></td>
                            <td>
                                <?php if ($currentUser['password_reset']): ?>
                                    <span>Temp Pass: <?php echo htmlspecialchars($currentUser['username']); ?></span>
                                <?php else: ?>
                                    <form action="manage_users.php" method="post" style="display: inline;">
                                        <input type="hidden" name="reset_password" value="1">
                                        <input type="hidden" name="user_id" value="<?php echo $currentUser['id']; ?>">
                                        <button type="submit" class="btn btn-warning btn-medium">Reset Password</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-medium">Access</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
    <script src="./assets/js/app.js"></script>
    <script>
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        var staffSelect = document.getElementById('staff_id');
        if (staffSelect.value === "") {
            e.preventDefault();
            alert('Please select a Staff Name member.');
        }
    });
    </script>
</body>
</html>