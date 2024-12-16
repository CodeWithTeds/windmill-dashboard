<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easy_park";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Validate that all required fields are present
    if (!isset($_POST['slot_name']) || !isset($_POST['location_address']) || 
        !isset($_POST['availability_status']) || !isset($_POST['slot_type']) || 
        !isset($_POST['parking_spot_area']) || !isset($_POST['slot_capacity'])) {
        throw new Exception("Missing required fields");
    }

    // Get and sanitize input data
    $slot_name = trim($_POST['slot_name']);
    $location_address = trim($_POST['location_address']);
    $availability_status = trim($_POST['availability_status']);
    $slot_type = trim($_POST['slot_type']);
    $parking_spot_area = floatval($_POST['parking_spot_area']);
    $slot_capacity = intval($_POST['slot_capacity']);
    $nearby_landmarks = trim($_POST['nearby_landmarks'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');

    $sql = "INSERT INTO places (
            slot_name, 
            location_address, 
            availability_status, 
            slot_type, 
            parking_spot_area, 
            slot_capacity, 
            nearby_landmarks, 
            geolocation_latitude, 
            geolocation_longitude, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssdisss", 
        $slot_name,
        $location_address,
        $availability_status,
        $slot_type,
        $parking_spot_area,
        $slot_capacity,
        $nearby_landmarks,
        $latitude,
        $longitude
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $response = [
        'success' => true,
        'message' => 'Parking slot added successfully!',
        'id' => $conn->insert_id
    ];
    
    echo json_encode($response);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Insert Place Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'post_data' => $_POST,
            'error' => $e->getMessage()
        ]
    ]);
}
?>
