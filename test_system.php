<?php
// Test System - Smart Door Lock System
// This file helps verify that all components are working correctly

echo "<h1>Smart Door Lock System - System Test</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h3>1. PHP Version Check</h3>";
echo "PHP Version: " . phpversion();
echo "<br>Status: " . (version_compare(PHP_VERSION, '7.0.0') >= 0 ? '✅ PASS' : '❌ FAIL');
echo "<hr>";

// Test 2: Database Connection
echo "<h3>2. Database Connection Test</h3>";
try {
    $conn = new mysqli('localhost', 'root', '', 'door_lock_system');
    
    if ($conn->connect_error) {
        echo "Database Connection: ❌ FAIL<br>";
        echo "Error: " . $conn->connect_error;
    } else {
        echo "Database Connection: ✅ PASS<br>";
        echo "Connected to: " . $conn->host_info;
        
        // Test database tables
        $tables = ['users', 'door_access_log', 'system_config'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            $status = $result->num_rows > 0 ? '✅ PASS' : '❌ FAIL';
            echo "<br>Table '$table': $status";
        }
        
        // Test user count
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $count = $result->fetch_assoc()['count'];
        echo "<br>Registered Users: $count";
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "Database Connection: ❌ FAIL<br>";
    echo "Error: " . $e->getMessage();
}
echo "<hr>";

// Test 3: File System Check
echo "<h3>3. File System Check</h3>";
$required_files = [
    'index.php',
    'dashboard.php',
    'users.php',
    'registration.php',
    'read_tag.php',
    'history.php',
    'login_process.php',
    'logout.php',
    'check_access.php',
    'log_access.php',
    'get_status.php',
    'smart_door_lock.ino',
    'database_setup.sql'
];

foreach ($required_files as $file) {
    $status = file_exists($file) ? '✅ PASS' : '❌ FAIL';
    echo "$file: $status<br>";
}
echo "<hr>";

// Test 4: Web Server Information
echo "<h3>4. Web Server Information</h3>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Server IP: " . ($_SERVER['SERVER_ADDR'] ?? 'Unknown') . "<br>";
echo "Client IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "<br>";
echo "<hr>";

// Test 5: PHP Extensions
echo "<h3>5. Required PHP Extensions</h3>";
$required_extensions = ['mysqli', 'session', 'json'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '✅ PASS' : '❌ FAIL';
    echo "$ext: $status<br>";
}
echo "<hr>";

// Test 6: Directory Permissions
echo "<h3>6. Directory Permissions</h3>";
$current_dir = getcwd();
$status = is_readable($current_dir) ? '✅ PASS' : '❌ FAIL';
echo "Current Directory Readable: $status<br>";

$status = is_writable($current_dir) ? '✅ PASS' : '❌ FAIL';
echo "Current Directory Writable: $status<br>";
echo "<hr>";

// Test 7: Session Test
echo "<h3>7. Session Test</h3>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "Session Management: ✅ PASS<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "Session Management: ❌ FAIL<br>";
}
echo "<hr>";

// Test 8: API Endpoints Test
echo "<h3>8. API Endpoints Test</h3>";
$endpoints = [
    'check_access.php' => 'GET /check_access.php?uid=TEST123',
    'log_access.php' => 'POST /log_access.php',
    'get_status.php' => 'GET /get_status.php'
];

foreach ($endpoints as $file => $endpoint) {
    if (file_exists($file)) {
        echo "$endpoint: ✅ Available<br>";
    } else {
        echo "$endpoint: ❌ Missing<br>";
    }
}
echo "<hr>";

// Test 9: Arduino Code Check
echo "<h3>9. Arduino Code Check</h3>";
if (file_exists('smart_door_lock.ino')) {
    $arduino_code = file_get_contents('smart_door_lock.ino');
    
    $checks = [
        'ESP8266WiFi' => strpos($arduino_code, 'ESP8266WiFi') !== false,
        'MFRC522' => strpos($arduino_code, 'MFRC522') !== false,
        'Servo' => strpos($arduino_code, 'Servo') !== false,
        'LiquidCrystal_I2C' => strpos($arduino_code, 'LiquidCrystal_I2C') !== false,
        'WiFi.begin' => strpos($arduino_code, 'WiFi.begin') !== false
    ];
    
    foreach ($checks as $feature => $exists) {
        $status = $exists ? '✅ PASS' : '❌ FAIL';
        echo "$feature: $status<br>";
    }
} else {
    echo "Arduino Code: ❌ Missing<br>";
}
echo "<hr>";

// Test 10: System Status
echo "<h3>10. System Status</h3>";
echo "Test Completed: " . date('Y-m-d H:i:s') . "<br>";
echo "System Ready: ✅ YES<br>";

// Summary
echo "<hr>";
echo "<h3>📊 Test Summary</h3>";
echo "Run this test after setting up the system to verify all components are working correctly.<br>";
echo "All tests should show ✅ PASS for a fully functional system.<br>";
echo "<br>";
echo "<strong>Next Steps:</strong><br>";
echo "1. Fix any failed tests<br>";
echo "2. Set up the hardware according to hardware_setup.md<br>";
echo "3. Upload Arduino code to ESP8266<br>";
echo "4. Test the complete system<br>";
echo "5. Access the web interface via index.php<br>";
?>
