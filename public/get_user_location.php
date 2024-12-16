<?php
// In a separate PHP file (e.g., get_user_location.php)
function getUserLocation() {
    // Check if geolocation is enabled and available
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Use a geolocation API to get coordinates
    $geo_url = "https://ipapi.co/{$ip}/json/";
    $geo_data = json_decode(file_get_contents($geo_url), true);
    
    return [
        'latitude' => $geo_data['latitude'] ?? 14.393344, // Default to Manila
        'longitude' => $geo_data['longitude'] ?? 121.0412
    ];
}

// Return JSON response
echo json_encode(getUserLocation());
?>