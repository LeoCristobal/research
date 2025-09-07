<?php
	$Write="<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
	file_put_contents('UIDContainer.php',$Write);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Home : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
    <style>
        body { background: #f8fafc; }
        .navbar-brand { font-weight: bold; }
        .main-card { max-width: 600px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
        .main-img { border-radius: 12px; }
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
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user data.php">User Data</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Registration</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
      
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Door Lock System with Esp8266</h2>
    <div class="alert alert-info text-center mb-4">This is the home page. Use the navigation bar to access user data, or registration features.</div>
    <img src="home ok ok.png" alt="" class="img-fluid main-img mb-3">
    <h5 class="text-center">Welcome to your  dashboard!</h5>
  </div>
</div>
</body>
</html>