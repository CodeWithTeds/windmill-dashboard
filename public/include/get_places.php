<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "easy_park";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            id, 
            slot_name, 
            location_address, 
            availability_status, 
            slot_type, 
            parking_spot_area,
            slot_capacity, 
            nearby_landmarks, 
            geolocation_latitude, 
            geolocation_longitude 
        FROM places";

$result = $conn->query($sql);

$places = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $places[] = $row;
    }
}

echo json_encode($places);
$conn->close();
?>