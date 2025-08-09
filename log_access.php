<?php
// Log door access events
header('Content-Type: text/plain');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'door_lock_system');

if ($conn->connect_error) {
    die("DB_ERROR");
}

// Get parameters from POST request
$uid = isset($_POST['uid']) ? $_POST['uid'] : '';
$accessType = isset($_POST['access_type']) ? $_POST['access_type'] : '';
$doorStatus = isset($_POST['door_status']) ? $_POST['door_status'] : '';

if (empty($uid) || empty($accessType)) {
    die("INVALID_PARAMS");
}

// Get user name if available
$userName = 'Unknown';
if ($accessType == 'Granted') {
    $stmt = $conn->prepare("SELECT name FROM users WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userName = $user['name'];
    }
    $stmt->close();
}

// Log the access event
$log_stmt = $conn->prepare("INSERT INTO door_access_log (uid, user_name, access_type, door_status) VALUES (?, ?, ?, ?)");
$log_stmt->bind_param("ssss", $uid, $userName, $accessType, $doorStatus);

if ($log_stmt->execute()) {
    echo "LOGGED";
} else {
    echo "LOG_ERROR";
}

$log_stmt->close();
$conn->close();
?>
