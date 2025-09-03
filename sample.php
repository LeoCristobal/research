<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['uid'])) {
        $uid = $_POST['uid'];
        echo "RFID UID Received: " . htmlspecialchars($uid);
    } else {
        echo "No UID received in POST.";
    }
} else {
    echo "Waiting for POST request...";
}
?>
