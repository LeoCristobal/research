<?php
require 'database.php';

$id = null;
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}

$data = null;
$msg = null;

if ($id !== null) {
    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Quote "id" for PostgreSQL
        $sql = 'SELECT * FROM user_info WHERE "id" = ?';
        $q = $pdo->prepare($sql);
        $q->execute(array($id));

        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();

        if (!$data || $data['name'] === null) {
            $msg = "The ID of your Card / KeyChain is not registered !!!";
            $data = [
                'id' => $id,
                'name' => "--------",
                'gender' => "--------",
                'email' => "--------",
                'mobile' => "--------"
            ];
        }
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
} else {
    $msg = "No ID provided.";
    $data = [
        'id' => null,
        'name' => "--------",
        'gender' => "--------",
        'email' => "--------",
        'mobile' => "--------"
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Read Tag User Data</title>
    <style>
        body { background: #f8fafc; }
        .main-card { max-width: 500px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
    </style>
</head>
<body>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">RFID Tag Details</h2>
    <div class="alert alert-info text-center mb-4">This page displays the details of the RFID tag you tapped. If the tag is registered, user details will be shown below.</div>
    <table class="table table-bordered">
      <tr><td>ID</td><td><?php echo $data['id'];?></td></tr>
      <tr><td>Name</td><td><?php echo $data['name'];?></td></tr>
      <tr><td>Gender</td><td><?php echo $data['gender'];?></td></tr>
      <tr><td>Email</td><td><?php echo $data['email'];?></td></tr>
      <tr><td>Mobile Number</td><td><?php echo $data['mobile'];?></td></tr>
    </table>
    <?php if ($msg) { echo '<div class="alert alert-warning text-center">' . $msg . '</div>'; } ?>
  </div>
</div>
</body>
</html>