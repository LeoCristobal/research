<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['uid'])) {
        $uid = $_POST['uid'];

        // isulat sa UIDContainer.php
        $file = fopen("UIDContainer.php", "w") or die("Unable to open file!");
        fwrite($file, "<?php $" . "uid='" . $uid . "'; ?>");
        fclose($file);

        echo "Received UID: " . $uid;
    } else {
        echo "No UID received";
    }
} else {
    echo "Invalid request method";
}
?>
