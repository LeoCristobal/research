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
        
        // Automatically update the Arduino code with current WiFi credentials and IP
        updateArduinoCode($wifi_name, $wifi_password);
        
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();

function updateArduinoCode($ssid, $password) {
    // Get the current computer's IP address automatically
    $serverIP = getServerIP();
    
    // Read the current Arduino file
    $arduinoFile = 'smart_door_lock_configured.ino';
    $template = file_get_contents($arduinoFile);
    
    // Replace WiFi credentials and server IP in the existing file
    $template = preg_replace('/const char\* ssid = "[^"]*";/', 'const char* ssid = "' . $ssid . '";', $template);
    $template = preg_replace('/const char\* password = "[^"]*";/', 'const char* password = "' . $password . '";', $template);
    $template = preg_replace('/const char\* host = "[^"]*";/', 'const char* host = "' . $serverIP . '";', $template);
    
    // Write the updated Arduino code back to the same file
    file_put_contents($arduinoFile, $template);
    
    // Log the update
    error_log("Arduino code updated - WiFi: $ssid, IP: $serverIP");
}

function getServerIP() {
    // Try to get the local IP address automatically
    if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '::1' && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
        return $_SERVER['SERVER_ADDR'];
    }
    
    // Fallback: try to get local IP from network interfaces
    $localIP = '192.168.1.100'; // Default fallback
    
    if (function_exists('shell_exec')) {
        // Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig | findstr "IPv4"');
            if (preg_match('/\d+\.\d+\.\d+\.\d+/', $output, $matches)) {
                $localIP = $matches[0];
            }
        }
        // Linux/Mac
        else {
            $output = shell_exec('hostname -I 2>/dev/null');
            if (preg_match('/\d+\.\d+\.\d+\.\d+/', $output, $matches)) {
                $localIP = trim($matches[0]);
            }
        }
    }
    
    return $localIP;
}
?>
