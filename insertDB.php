<?php
require 'database.php';

if (!empty($_POST)) {
    // Keep track of post values
    $name   = $_POST['name'];
    $id     = $_POST['id'];        // Be careful: 'id' is reserved in PostgreSQL
    $gender = $_POST['gender'];
    $email  = $_POST['email'];
    $mobile = $_POST['mobile'];

    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Use double quotes for reserved keywords
        $sql = 'INSERT INTO user_info (name, id, gender, email, mobile) VALUES (?, ?, ?, ?, ?)';
        $q = $pdo->prepare($sql);
        $q->execute(array($name, $id, $gender, $email, $mobile));

        Database::disconnect();
        header("Location: user data.php");
        exit;
    } catch (PDOException $e) {
        die("Error inserting data: " . $e->getMessage());
    }
}
?>
