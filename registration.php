<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <title>Registration : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
  <style>
    body { background: #f8fafc; }
    .navbar-brand { font-weight: bold; }
    .main-card { max-width: 600px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
  </style>
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
          <li class="nav-item"><a class="nav-link active" href="registration.php">Registration</a></li>
          <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="card main-card p-4 mt-5">
      <h2 class="text-center mb-3">Registration Form</h2>
      <div class="alert alert-info text-center mb-4">
        Register a new user by scanning their RFID and filling out the form.
      </div>
      <form class="row g-3" action="insertDB.php" method="post">
        <div class="col-12">
          <label for="getUID" class="form-label">ID</label>
          <input type="text" name="id" id="getUID" class="form-control" readonly required>
        </div>
        <div class="col-12">
          <label for="name" class="form-label">Name</label>
          <input name="name" type="text" class="form-control" required>
        </div>
        <div class="col-12">
          <label for="gender" class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="col-12">
          <label for="email" class="form-label">Email Address</label>
          <input name="email" type="email" class="form-control" required>
        </div>
        <div class="col-12">
          <label for="mobile" class="form-label">Mobile Number</label>
          <input name="mobile" type="text" class="form-control" required>
        </div>
        <div class="col-12 text-center">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Auto-load UID from UIDContainer.php every second
    function loadUID() {
      fetch("UIDContainer.php")
        .then(response => response.text())
        .then(data => {
          let uidField = document.getElementById("getUID");
          if (data.trim() !== "" && uidField.value !== data.trim()) {
            uidField.value = data.trim();
          }
        });
    }
    setInterval(loadUID, 1000);
  </script>
</body>
</html>
