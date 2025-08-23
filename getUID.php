<?php
if (isset($_POST['UIDresult']) && !empty($_POST['UIDresult'])) {
    $UIDresult = $_POST['UIDresult'];
    // save latest UID sa file
    file_put_contents('/tmp/UIDContainer.txt', $UIDresult);
    echo "Received UID: " . $UIDresult;
} else {
    echo "No UID received";
}
?>
