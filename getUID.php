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

    // Log to access_log
    if ($data) {
        // User found, log with user_id
        $logSql = "INSERT INTO access_log (user_id, rfid_id, action) VALUES (?, ?, 'tap')";
        $logQ = $pdo->prepare($logSql);
        $logQ->execute([$data['user_id'], $UIDresult]);
        echo "AUTHORIZED";
    } else {
        // User not found, log with NULL user_id
        $logSql = "INSERT INTO access_log (user_id, rfid_id, action) VALUES (NULL, ?, 'tap')";
        $logQ = $pdo->prepare($logSql);
        $logQ->execute([$UIDresult]);
        echo "UNAUTHORIZED";
    }

    Database::disconnect();
} else {
    echo "No UID received.";
}
?>
