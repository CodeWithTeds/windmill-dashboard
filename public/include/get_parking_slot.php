<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "easy_park");

if ($conn->connect_error) {
    die(json_encode([
        'error' => 'Connection failed', 
        'details' => $conn->connect_error
    ]));
}

// Check if ID is set
if (!isset($_GET['id'])) {
    die(json_encode([
        'error' => 'No ID provided',
        'message' => 'ID parameter is missing'
    ]));
}

$id = $_GET['id'];

// Prepare and execute the query
$sql = "SELECT * FROM places WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode([
        'error' => 'Prepare failed',
        'details' => $conn->error
    ]));
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die(json_encode([
        'error' => 'Query execution failed',
        'details' => $stmt->error
    ]));
}

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([
        'error' => 'Parking slot not found',
        'id' => $id
    ]);
}

$stmt->close();
$conn->close();
?>