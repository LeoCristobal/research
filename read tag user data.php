<?php
require 'database.php';
if(isset($_GET['id'])){
    $uid = $_GET['id'];
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT * FROM user_info WHERE id = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user){
        echo json_encode($user);
    } else {
        echo "NOT_REGISTERED";
    }
    Database::disconnect();
}
?>
