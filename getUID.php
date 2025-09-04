<?php
require 'database.php';

if (isset($_POST["uid"])) {
    $UIDresult = $_POST["uid"];

    // Save UID sa UIDContainer.php
    $Write = "<?php $" . "UIDresult='" . $UIDresult . "'; " . "echo $" . "UIDresult;" . " ?>";
    file_put_contents('UIDContainer.php', $Write);

    // Connect sa DB
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check kung existing sa user_info table (id column)
    $sql = "SELECT * FROM user_info WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$UIDresult]);
    $data = $q->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo "AUTHORIZED";
    } else {
        echo "UNAUTHORIZED";
    }

    Database::disconnect();
} else {
    echo "No UID received.";
}
?>
