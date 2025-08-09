<?php
// Check RFID card access
header('Content-Type: text/plain');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'door_lock_system');

if ($conn->connect_error) {
    die("DB_ERROR");
}

// Get UID from request
$uid = isset($_GET['uid']) ? $_GET['uid'] : '';

if (empty($uid)) {
    die("INVALID_UID");
}

// Check if user exists and is authorized
$stmt = $conn->prepare("SELECT name FROM users WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userName = $user['name'];
    
    // Log the access attempt
    $log_stmt = $conn->prepare("INSERT INTO door_access_log (uid, user_name, access_type, door_status) VALUES (?, ?, 'Granted', 'Opened')");
    $log_stmt->bind_param("ss", $uid, $userName);
    $log_stmt->execute();
    $log_stmt->close();
    
    echo "GRANTED:" . $userName;
} else {
    // Log denied access
    $log_stmt = $conn->prepare("INSERT INTO door_access_log (uid, user_name, access_type, door_status) VALUES (?, 'Unknown', 'Denied', 'Closed')");
    $log_stmt->bind_param("s", $uid);
    $log_stmt->execute();
    $log_stmt->close();
    
    echo "DENIED";
}

$stmt->close();
$conn->close();
?>
