<?php
// Initialize UIDContainer.php if not exists
if(!file_exists('UIDContainer.php')) {
    $Write="<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
    file_put_contents('UIDContainer.php',$Write);
}

// Handle resetUID POST
if(isset($_POST['resetUID'])) {
    $Write="<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
    file_put_contents('UIDContainer.php',$Write);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Read Tag : NodeMCU V3 ESP8266 / ESP12E</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8fafc; }
.navbar-brand { font-weight: bold; }
.main-card { max-width: 600px; margin: 40px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.07); border-radius: 16px; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">RFID System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user data.php">User Data</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Registration</a></li>
        <li class="nav-item"><a class="nav-link active" href="read tag.php">Read Tag ID</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Read Tag</h2>
    <div class="alert alert-info text-center mb-4">
        Tap your RFID card or keychain. Registered info will show below.
    </div>
    <div id="show_user_data">
      <table class="table table-bordered">
        <tr><td>ID</td><td id="rfid-id">--------</td></tr>
        <tr><td>Name</td><td id="rfid-name">--------</td></tr>
        <tr><td>Gender</td><td id="rfid-gender">--------</td></tr>
        <tr><td>Email</td><td id="rfid-email">--------</td></tr>
        <tr><td>Mobile Number</td><td id="rfid-mobile">--------</td></tr>
      </table>
    </div>
  </div>
</div>

<p id="getUID" hidden></p>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    var lastUID = "";
    var resetTimer;

    // Poll UIDContainer.php every 500ms
    setInterval(loadUID, 500);

    function loadUID() {
        $.get('UIDContainer.php', function(uid) {
            uid = uid.trim();
            if(uid && uid !== lastUID) {
                lastUID = uid;
                fetchUserData(uid);
            }
        });
    }

    function fetchUserData(uid) {
        $.get("read tag user data.php?id=" + uid, function(data) {
            try {
                var user = JSON.parse(data);
                $('#rfid-id').text(user.id || uid);
                $('#rfid-name').text(user.name || "Unknown");
                $('#rfid-gender').text(user.gender || "Unknown");
                $('#rfid-email').text(user.email || "-");
                $('#rfid-mobile').text(user.mobile || "-");
            } catch (e) {
                // Card not registered
                $('#rfid-id').text(uid);
                $('#rfid-name').text("Card not registered");
                $('#rfid-gender').text("-");
                $('#rfid-email').text("-");
                $('#rfid-mobile').text("-");
            }

            // Reset UIDContainer.php immediately
            $.post('', { resetUID: true });

            // Clear table after 5 seconds
            clearTimeout(resetTimer);
            resetTimer = setTimeout(resetTable, 5000);
        });
    }

    function resetTable() {
        $('#rfid-id').text("--------");
        $('#rfid-name').text("--------");
        $('#rfid-gender').text("--------");
        $('#rfid-email').text("--------");
        $('#rfid-mobile').text("--------");
        lastUID = "";
    }
});
</script>

</body>
</html>
