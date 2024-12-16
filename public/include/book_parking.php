<?php
session_start();
require_once '../config/Database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Get POST data
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

// Validate input
if (!isset($data['parking_id']) || !isset($data['time_in']) || !isset($data['time_out'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$parking_id = $data['parking_id'];
$time_in = new DateTime($data['time_in']);
$time_out = new DateTime($data['time_out']);
$user_id = $_SESSION['user_id'];

// Validate booking duration
$duration = $time_out->diff($time_in);
$hours = $duration->h + ($duration->days * 24);

if ($hours < 1) {
    echo json_encode(['success' => false, 'message' => 'Minimum booking duration is 1 hour']);
    exit;
}

if ($hours > 24) {
    echo json_encode(['success' => false, 'message' => 'Maximum booking duration is 24 hours']);
    exit;
}

// Create database connection
$database = new Database();
$conn = $database->getConnection();

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if the parking spot exists and get its details
    $stmt = $conn->prepare("SELECT id, availability_status, slot_capacity FROM places WHERE id = ?");
    $stmt->execute([$parking_id]);
    $place = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$place) {
        throw new Exception('Parking spot not found');
    }

    // Check if there's any capacity left
    if ($place['slot_capacity'] <= 0) {
        throw new Exception('Parking spot is full');
    }

    // Check for overlapping bookings
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM parking_bookings 
        WHERE parking_id = ? 
        AND status = 'confirmed'
        AND (
            (time_in BETWEEN ? AND ?) OR
            (time_out BETWEEN ? AND ?) OR
            (time_in <= ? AND time_out >= ?)
        )
    ");
    $stmt->execute([
        $parking_id,
        $time_in->format('Y-m-d H:i:s'),
        $time_out->format('Y-m-d H:i:s'),
        $time_in->format('Y-m-d H:i:s'),
        $time_out->format('Y-m-d H:i:s'),
        $time_in->format('Y-m-d H:i:s'),
        $time_out->format('Y-m-d H:i:s')
    ]);
    
    $overlapping_bookings = $stmt->fetchColumn();
    
    if ($overlapping_bookings >= $place['slot_capacity']) {
        throw new Exception('This time slot is already fully booked');
    }

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO parking_bookings (
            user_id, 
            parking_id, 
            time_in, 
            time_out, 
            status
        ) VALUES (?, ?, ?, ?, 'confirmed')
    ");
    $stmt->execute([
        $user_id,
        $parking_id,
        $time_in->format('Y-m-d H:i:s'),
        $time_out->format('Y-m-d H:i:s')
    ]);

    // Update capacity
    $new_capacity = $place['slot_capacity'] - 1;
    $new_status = $new_capacity <= 0 ? 'occupied' : 'available';
    
    $stmt = $conn->prepare("
        UPDATE places 
        SET slot_capacity = ?,
            availability_status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$new_capacity, $new_status, $parking_id]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($conn) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    if ($conn) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}