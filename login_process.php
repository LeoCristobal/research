<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'door_lock_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wifi_name = $_POST['wifi_name'];
    $wifi_password = $_POST['wifi_password'];
    
    // Store WiFi credentials in database
    $stmt = $conn->prepare("INSERT INTO system_config (wifi_name, wifi_password) VALUES (?, ?) ON DUPLICATE KEY UPDATE wifi_name = ?, wifi_password = ?");
    $stmt->bind_param("ssss", $wifi_name, $wifi_password, $wifi_name, $wifi_password);
    
    if ($stmt->execute()) {
        // Store in session
        $_SESSION['wifi_name'] = $wifi_name;
        $_SESSION['wifi_password'] = $wifi_password;
        
        // Generate Arduino code with WiFi credentials
        generateArduinoCode($wifi_name, $wifi_password);
        
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();

function generateArduinoCode($ssid, $password) {
    // Get the current computer's IP address
    $serverIP = $_SERVER['SERVER_ADDR'] ?: '192.168.1.100';
    
    // Read the Arduino template
    $template = file_get_contents('smart_door_lock.ino');
    
    // Replace WiFi credentials and server IP
    $template = str_replace('YOUR_WIFI_NAME', $ssid, $template);
    $template = str_replace('YOUR_WIFI_PASSWORD', $password, $template);
    $template = str_replace('192.168.1.100', $serverIP, $template);
    
    // Write the updated Arduino code
    file_put_contents('smart_door_lock_configured.ino', $template);
}
?>
