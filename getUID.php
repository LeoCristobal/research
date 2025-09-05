<?php
require 'database.php';

if (isset($_POST["uid"])) {
    $UIDresult = $_POST["uid"];

    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if UID exists
    $sql = "SELECT * FROM user_info WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute([$UIDresult]);
    $data = $q->fetch(PDO::FETCH_ASSOC);

    // Determine action and user_id
    if ($data) {
        $action = "AUTHORIZED";
        $user_id = $data['user_id'];
    } else {
        $action = "UNAUTHORIZED";
        $user_id = null; // unknown user
    }

    // ✅ Always log to access_log
    $stmt = $pdo->prepare("INSERT INTO access_log (user_id, rfid_id, action) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $UIDresult, $action]);

    // Save UID to UIDContainer.php for registration.php
    $write = "<?php $" . "UIDresult='" . $UIDresult . "'; echo $" . "UIDresult; ?>";
    file_put_contents('UIDContainer.php', $write);

    echo $action;

    Database::disconnect();
} else {
    echo "No UID received.";
}
?>
