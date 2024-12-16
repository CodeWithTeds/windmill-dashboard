<?php
require('config/Database.php');
require('models/User.php');

// Database connection
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Handle Edit User Action
if (isset($_POST['action']) && $_POST['action'] == 'edit' && isset($_POST['id']) && isset($_POST['username']) && isset($_POST['email'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Update user data
    if ($user->updateUser($id, $username, $email)) {
        header("Location: manage-users.php?status=success");
        exit;
    } else {
        header("Location: manage-users.php?status=error");
        exit;
    }
}

// Handle Delete User Action
if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete user data
    if ($user->deleteUser($id)) {
        header("Location: admin-users.php?status=deleted");
        exit;
    } else {
        header("Location: admin-users.php?status=error");
        exit;
    }
}

// If no valid action, redirect with an invalid status
header("Location: admin-users.php?status=invalid");
exit;
?>
