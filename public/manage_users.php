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
$users = $user->getAllUsers();
$availableStaff = $user->getStaffWithoutUsers();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $result = $user->addUser($_POST);
        if ($result === true) {
            $success = "User added successfully.";
        } else {
            $error = $result;
        }
    }
    // Refresh user list and available staff after add
    $users = $user->getAllUsers();
    $availableStaff = $user->getStaffWithoutUsers();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Silver System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Manage Users</h1>
            <div>
                <a href="admin.php" class="btn btn-primary">Back to Admin Dashboard</a>
                <a href="logout.php" class="btn btn-danger">LOGOUT</a>
            </div>
        </header>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <section class="users-section">
            <h2>Add New User</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Associated Staff</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <form action="manage_users.php" method="post" class="add-user-form">
                            <input type="hidden" name="add_user" value="1">
                            <td><input type="text" id="username" name="username" required maxlength="30" class="wide-input"></td>
                            <td>
                                <select name="staff_id" id="staff_id">
                                    <option value="">None (Admin)</option>
                                    <?php foreach ($availableStaff as $staff): ?>
                                        <option value="<?php echo $staff['staff_id']; ?>">
                                            <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><button type="submit" class="btn btn-primary btn-small">Add User</button></td>
                        </form>
                    </tr>
                </tbody>
            </table>

            <h2>Current Users</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Associated Staff</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['staff_id'] ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : 'Admin'; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-small">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
    <script src="../assets/js/app.js"></script>
</body>
</html>