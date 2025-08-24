<?php
// rfid.php

// Check if UID is sent via GET or POST
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
} elseif (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
} else {
    die("No UID received.");
}

// Example: show the UID
echo "RFID UID Received: " . htmlspecialchars($uid);

// (Optional) Save UID to database
/*
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO rfid_logs (uid) VALUES ('$uid')";
if ($conn->query($sql) === TRUE) {
    echo " - Saved to database.";
} else {
    echo " - Error: " . $conn->error;
}

$conn->close();
*/
?>
