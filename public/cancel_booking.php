<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Check if booking_id was provided
if (!isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID required']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easy_park";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First, check if the booking exists and its current status
    $checkSql = "SELECT status FROM parking_bookings WHERE id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
    $booking = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    if ($booking['status'] === 'cancelled') {
        echo json_encode(['success' => false, 'message' => 'Booking is already cancelled']);
        exit;
    }
    
    // Update booking status
    $sql = "UPDATE parking_bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        // For debugging, let's see what data we're working with
        error_log("Booking ID: " . $_POST['booking_id']);
        error_log("User ID: " . $_SESSION['user_id']);
        echo json_encode(['success' => false, 'message' => 'Unable to cancel booking. Please try again.']);
    }
} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} 