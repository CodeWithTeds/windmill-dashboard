<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "easy_park");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

$id = $_POST['id'];
$sql = "DELETE FROM places WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}

$stmt->close();
$conn->close();
?>