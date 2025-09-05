<?php
require 'database.php';

// Get user_id from URL
$id = null;
if (!empty($_GET['user_id'])) {
    $id = $_GET['user_id'];
}

$data = null;
$error = null;

if ($id !== null) {
    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT * FROM user_info WHERE user_id = ?';
        $q = $pdo->prepare($sql);
        $q->execute([$id]);
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();

        if (!$data) {
            $error = 'User not found. (Debug: user_id=' . htmlspecialchars($id) . ')';
        }
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
} else {
    die("No user_id provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Edit : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
    <style>
        body { background: #f8fafc; }
        .main-card { max-width: 600px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
    </style>
</head>
<body>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Edit User Data</h2>
    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php else: ?>
    <form class="row g-3" action="user data edit tb.php?user_id=<?php echo $id ?>" method="post">
      <div class="col-12">
        <label for="id" class="form-label">ID</label>
        <input name="id" type="text" class="form-control" value="<?php echo htmlspecialchars($data['id']); ?>" readonly>
      </div>
      <div class="col-12">
        <label for="name" class="form-label">Name</label>
        <input name="name" type="text" class="form-control" value="<?php echo htmlspecialchars($data['name']); ?>" required>
      </div>
      <div class="col-12">
        <label for="gender" class="form-label">Gender</label>
        <select name="gender" id="mySelect" class="form-select">
          <option value="Male" <?php if($data['gender']=="Male") echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if($data['gender']=="Female") echo 'selected'; ?>>Female</option>
        </select>
      </div>
      <div class="col-12">
        <label for="email" class="form-label">Email Address</label>
        <input name="email" type="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required>
      </div>
      <div class="col-12">
        <label for="mobile" class="form-label">Mobile Number</label>
        <input name="mobile" type="text" class="form-control" value="<?php echo htmlspecialchars($data['mobile']); ?>" required>
      </div>
      <div class="col-12 text-center">
        <button type="submit" class="btn btn-success">Update</button>
        <a class="btn btn-secondary" href="user data.php">Back</a>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
