<?php
session_start();
require_once '../../config/Database.php';

header('Content-Type: application/json');

$parking_id = $_GET['parking_id'] ?? null;

if (!$parking_id) {
    echo json_encode(['error' => 'No parking ID provided']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get current timestamp
    $current_time = date('Y-m-d H:i:s');

    // Count current active bookings
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM parking_bookings 
        WHERE parking_id = ? 
        AND status = 'confirmed'
        AND time_in <= ? 
        AND time_out >= ?
    ");
    
    $stmt->execute([$parking_id, $current_time, $current_time]);
    $current_bookings = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'current_bookings' => $current_bookings
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
