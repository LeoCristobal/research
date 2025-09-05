<?php
require 'database.php';

$user_id = $_GET['user_id'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Delete access_log muna (para walang foreign key error)
        $stmt = $pdo->prepare("DELETE FROM access_log WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user_info
        $stmt = $pdo->prepare("DELETE FROM user_info WHERE user_id = ?");
        $stmt->execute([$user_id]);

        Database::disconnect();

        // Redirect sa user data page
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
  <title>Delete User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { background: #f8fafc; }
    .main-card { max-width: 500px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
  </style>
</head>
<body>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Delete User Confirmation</h2>
    <div class="alert alert-warning text-center mb-4">
      You are about to delete a user. This action cannot be undone. Please confirm below.
    </div>
    <form class="text-center" action="user data delete page.php" method="post">
      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"/>
      <p class="alert alert-danger">Are you sure you want to delete this user?</p>
      <div class="d-flex justify-content-center gap-3">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a class="btn btn-secondary" href="user data.php">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
