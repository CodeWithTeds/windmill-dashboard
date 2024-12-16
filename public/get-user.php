<?php
require('config/Database.php');
require('models/User.php');

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if (isset($_GET['id'])) {
    $userData = $user->getUserById($_GET['id']);
    if ($userData) {
        echo json_encode(['success' => true, 'user' => $userData]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
