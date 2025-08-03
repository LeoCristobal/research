<?php
// Check if the request is POST and UIDresult is set
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["UIDresult"])) {
    $UIDresult = $_POST["UIDresult"];
    
    // Write the UID to UIDContainer.php
    $write = "<?php $" . "UIDresult='" . $UIDresult . "'; echo $" . "UIDresult; ?>";
    file_put_contents("UIDContainer.php", $write);

    // Respond to NodeMCU
    echo "UID received and written: " . $UIDresult;
} else {
    // Handle invalid requests
    echo "Invalid request method or UIDresult not set.";
}
?>
