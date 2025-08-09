<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['wifi_name'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'door_lock_system');

if ($conn->connect_error) {
    http_response_code(500);
    exit('Database connection failed');
}

// Get door status (this would typically come from ESP8266)
$door_status = "Locked"; // Default status

// Get user count
$user_result = $conn->query("SELECT COUNT(*) as count FROM users");
$user_count = $user_result->fetch_assoc()['count'];

// Get recent access status
$access_result = $conn->query("SELECT access_granted FROM access_history ORDER BY access_time DESC LIMIT 1");
if ($access_result->num_rows > 0) {
    $last_access = $access_result->fetch_assoc();
    $door_status = $last_access['access_granted'] ? "Open" : "Locked";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'door_status' => $door_status,
    'user_count' => $user_count,
    'timestamp' => date('Y-m-d H:i:s')
]);

$conn->close();
?>
