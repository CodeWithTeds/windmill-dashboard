<?php
session_start();
require_once '../config/Database.php';

header('Content-Type: application/json');

// Get POST data
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

// Validate input
if (!isset($data['parking_id']) || !isset($data['user_latitude']) || !isset($data['user_longitude'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    // Get parking spot location
    $stmt = $conn->prepare("SELECT geolocation_latitude, geolocation_longitude FROM places WHERE id = ?");
    $stmt->execute([$data['parking_id']]);
    $place = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$place) {
        throw new Exception('Parking spot not found');
    }

    // Calculate distance
    $earth_radius = 6371000; // meters
    $lat1 = deg2rad($data['user_latitude']);
    $lon1 = deg2rad($data['user_longitude']);
    $lat2 = deg2rad($place['geolocation_latitude']);
    $lon2 = deg2rad($place['geolocation_longitude']);
    
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    
    $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earth_radius * $c;

    if ($distance > 100) { // 100 meters radius
        echo json_encode([
            'success' => false, 
            'message' => 'You must be within 100 meters of the parking location to time in'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Location verified'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 