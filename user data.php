<?php
// Save empty UID container in a safe writable directory
$Write = "<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
file_put_contents('UIDContainer.php', $Write);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>User Data : NodeMCU V3 ESP8266 / ESP12E with MySQL Database</title>
    <style>
        body { background: #f8fafc; }
        .navbar-brand { font-weight: bold; }
        .main-card { max-width: 1100px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Door Lock System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="user_data.php">User Data</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Registration</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">User Data Table</h2>
    <div class="alert alert-info text-center mb-4">This page displays all registered users. You can edit or delete users using the buttons in the Action column. The table is responsive and mobile-friendly.</div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>Name</th>
            <th>ID</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
            include 'database.php';
            $pdo = Database::connect();
            $sql = 'SELECT * FROM user_info ORDER BY name ASC';
            foreach ($pdo->query($sql) as $row) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['name']) . '</td>';
              echo '<td>' . htmlspecialchars($row['id']) . '</td>';
              echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
              echo '<td>' . htmlspecialchars($row['email']) . '</td>';
              echo '<td>' . htmlspecialchars($row['mobile']) . '</td>';
              echo '<td>';
              echo '<a class="btn btn-success btn-sm me-2" href="user data edit page.php?user_id=' . urlencode($row['user_id']) . '">Edit</a>';
              echo '<a class="btn btn-danger btn-sm" href="user data delete page.php?user_id=' . urlencode($row['user_id']) . '">Delete</a>';
              echo '</td>';
              echo '</tr>';
            }
            Database::disconnect();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
