<?php
require 'database.php';

// AJAX request para sa auto-update
if (isset($_GET['fetch'])) {
    $pdo = Database::connect();
    $sql = 'SELECT access_log.timestamp, access_log.rfid_id, user_info.name 
            FROM access_log 
            LEFT JOIN user_info ON access_log.rfid_id = user_info.id 
            ORDER BY access_log.timestamp DESC';
    foreach ($pdo->query($sql) as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['name'] ?? 'Unknown') . '</td>';
        echo '<td>' . htmlspecialchars($row['rfid_id']) . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($row['timestamp'])) . '</td>';
        echo '</tr>';
    }
    Database::disconnect();
    exit; // Important: para AJAX lang ang mag-return
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <title>History : NodeMCU V3 ESP8266 / ESP12E with Database</title>
  <style>
    body { background: #f8fafc; }
    .navbar-brand { font-weight: bold; }
    .main-card { max-width: 1100px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
  </style>
  <script>
    async function fetchHistory() {
      try {
        const response = await fetch('history.php?fetch=1');
        const html = await response.text();
        document.getElementById('history-body').innerHTML = html;
      } catch (err) {
        console.error('Error fetching history:', err);
      }
    }
    setInterval(fetchHistory, 1000); // Auto-refresh every 2 seconds
    window.onload = fetchHistory;
  </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Door Lock System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user data.php">User Data</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Registration</a></li>
        <li class="nav-item"><a class="nav-link active" href="history.php">History</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Access History</h2>
    <div class="alert alert-info text-center mb-4">
      This page shows the history of RFID taps, including the date/time, UID, and user name (if available).
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>Name</th>
            <th>UID</th>
            <th>Date and Time</th>
          </tr>
        </thead>
        <tbody id="history-body">
          <!-- Initial empty body, JS will populate -->
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
