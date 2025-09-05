<?php
require 'database.php';

if (!empty($_POST)) {
    $id     = $_POST['id'];
    $name   = $_POST['name'];
    $gender = $_POST['gender'];
    $email  = $_POST['email'];
    $mobile = $_POST['mobile'];

    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Use id without quotes for compatibility
        $sql = 'UPDATE user_info SET name = ?, gender = ?, email = ?, mobile = ? WHERE id = ?';
        $q = $pdo->prepare($sql);
        $q->execute([$name, $gender, $email, $mobile, $id]);
        Database::disconnect();
        // Redirect to user data page immediately after update
        header("Location: user data.php");
        exit;
    } catch (PDOException $e) {
        die("Error updating data: " . $e->getMessage());
    }
} else {
    die("No POST data received.");
}
?>