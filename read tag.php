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
    <script src="jquery.min.js"></script>
    <title>Read Tag : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
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
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user data.php">User Data</a></li>
        <li class="nav-item"><a class="nav-link" href="registration.php">Registration</a></li>
        <li class="nav-item"><a class="nav-link active" href="read tag.php">Read Tag ID</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card main-card p-4 mt-5">
    <h2 class="text-center mb-3">Read Tag</h2>
    <div class="alert alert-info text-center mb-4">Tap your RFID card or keychain to the reader. The details will be shown below if registered.</div>
    <div id="show_user_data">
      <!-- User data will be loaded here -->
      <form>
        <table class="table table-bordered">
          <tr><td>ID</td><td id="rfid-id">--------</td></tr>
          <tr><td>Name</td><td id="rfid-name">--------</td></tr>
          <tr><td>Gender</td><td id="rfid-gender">--------</td></tr>
          <tr><td>Email</td><td id="rfid-email">--------</td></tr>
          <tr><td>Mobile Number</td><td id="rfid-mobile">--------</td></tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
    $("#getUID").load("UIDContainer.php");
    setInterval(function() {
        $("#getUID").load("UIDContainer.php");
    }, 500);
});
var myVar = setInterval(myTimer, 1000);
var myVar1 = setInterval(myTimer1, 1000);
var oldID="";
clearInterval(myVar1);
function myTimer() {
    var getID=document.getElementById("getUID").innerHTML;
    oldID=getID;
    if(getID!="") {
        myVar1 = setInterval(myTimer1, 500);
        showUser(getID);
        clearInterval(myVar);
    }
}
function myTimer1() {
    var getID=document.getElementById("getUID").innerHTML;
    if(oldID!=getID) {
        myVar = setInterval(myTimer, 500);
        clearInterval(myVar1);
    }
}
function showUser(str) {
    if (str == "") {
        document.getElementById("show_user_data").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("show_user_data").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","read tag user data.php?id="+str,true);
        xmlhttp.send();
    }
}
</script>
<p id="getUID" hidden></p>
</body>
</html>