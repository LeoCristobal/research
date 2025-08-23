<?php
if (isset($_POST['UIDresult']) && !empty($_POST['UIDresult'])) {
    $UIDresult = $_POST['UIDresult'];
    $Write = "<?php $" . "UIDresult='" . $UIDresult . "'; " . "echo $" . "UIDresult;" . " ?>";

    // Save to tmp folder (ephemeral in Render)
    file_put_contents('/tmp/UIDContainer.php', $Write);

    // Response to ESP8266
    echo "Received UID: " . $UIDresult;
} else {
    echo "No UID received";
}
?>
