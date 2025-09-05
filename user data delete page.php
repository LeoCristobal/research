<?php
require 'database.php';

$id = 0;

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}

if (!empty($_POST)) {
    // keep track post values
    $id = $_POST['id'];

    try {
        // delete data
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Use id without quotes for compatibility
        $sql = 'DELETE FROM user_info WHERE id = ?';
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $deletedRows = $q->rowCount();
        Database::disconnect();
        if ($deletedRows === 0) {
            die('No user deleted. (Debug: id=' . htmlspecialchars($id) . ')');
        }
        // Redirect to user_data.php (no spaces)
        header("Location: user data.php");
        exit;
    } catch (PDOException $e) {
        die("Error deleting data: " . $e->getMessage());
    }
}
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Delete User : NodeMCU V3 ESP8266 / ESP12E with Database</title>
    <style>
        body { background: #f8fafc; }
        .main-card { max-width: 500px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
    </style>
</head>
<body>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Delete User Confirmation</h2>
    <div class="alert alert-warning text-center mb-4">You are about to delete a user. This action cannot be undone. Please confirm below.</div>
    <form class="text-center" action="user data delete page.php" method="post">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>"/>
      <p class="alert alert-danger">Are you sure you want to delete this user?<br>This action cannot be undone.</p>
      <div class="d-flex justify-content-center gap-3">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a class="btn btn-secondary" href="user data.php">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>