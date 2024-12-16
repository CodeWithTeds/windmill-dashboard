<?php
header('Content-Type: application/json');

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

    // Check if edit_id exists
    if (!isset($_POST['edit_id'])) {
        throw new Exception("No parking slot ID provided");
    }

    $id = $_POST['edit_id'];
    $slot_name = $_POST['slot_name'];
    $location_address = $_POST['location_address'];
    $availability_status = $_POST['availability_status'];
    $slot_type = $_POST['slot_type'];
    $parking_spot_area = $_POST['parking_spot_area'];
    $slot_capacity = $_POST['slot_capacity'];
    $nearby_landmarks = $_POST['nearby_landmarks'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $sql = "UPDATE places SET 
            slot_name = ?,
            location_address = ?,
            availability_status = ?,
            slot_type = ?,
            parking_spot_area = ?,
            slot_capacity = ?,
            nearby_landmarks = ?,
            geolocation_latitude = ?,
            geolocation_longitude = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdisssi", 
        $slot_name,
        $location_address,
        $availability_status,
        $slot_type,
        $parking_spot_area,
        $slot_capacity,
        $nearby_landmarks,
        $latitude,
        $longitude,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Parking slot updated successfully'
        ]);
    } else {
        throw new Exception("Error updating record: " . $conn->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>