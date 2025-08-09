<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['wifi_name'])) {
    header('Location: index.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'door_lock_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    
    // Check if UID already exists
    $check_stmt = $conn->prepare("SELECT uid FROM users WHERE uid = ?");
    $check_stmt->bind_param("s", $uid);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = "RFID card with UID $uid is already registered!";
        $message_type = "danger";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (uid, name, gender, email, mobile, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $uid, $name, $gender, $email, $mobile);
        
        if ($stmt->execute()) {
            $message = "User registered successfully! RFID card $uid is now active.";
            $message_type = "success";
            
            // Clear form
            $_POST = array();
        } else {
            $message = "Error registering user: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Smart Door Lock System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .nav-link {
            color: #495057 !important;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #667eea !important;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .registration-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .rfid-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .rfid-input {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
        }
        .rfid-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-lock me-2"></i>Smart Door Lock
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-1"></i>User Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="registration.php">
                            <i class="fas fa-user-plus me-1"></i>Registration
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="read_tag.php">
                            <i class="fas fa-id-card me-1"></i>Read Tag ID
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">
                            <i class="fas fa-history me-1"></i>History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-5 fw-bold">User Registration</h1>
            <p class="lead">Register new users with RFID cards for door access</p>
        </div>
    </section>

    <!-- RFID Reading Section -->
    <section class="container mb-4">
        <div class="rfid-section text-center">
            <h3><i class="fas fa-id-card me-2"></i>RFID Card Reader</h3>
            <p class="lead mb-4">Tap an RFID card on the reader to automatically fill the UID field</p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-dark">
                            <i class="fas fa-microchip"></i>
                        </span>
                        <input type="text" class="form-control rfid-input" id="rfidInput" placeholder="Waiting for RFID card..." readonly>
                        <button class="btn btn-light" type="button" onclick="simulateRFID()">
                            <i class="fas fa-sim-card me-1"></i>Simulate
                        </button>
                    </div>
                    <small class="text-white-50">The UID will be automatically detected when you tap a card</small>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form -->
    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card registration-card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>User Information
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="uid" class="form-label">RFID UID *</label>
                                    <input type="text" class="form-control" id="uid" name="uid" value="<?php echo isset($_POST['uid']) ? htmlspecialchars($_POST['uid']) : ''; ?>" required readonly>
                                    <div class="form-text">This field is automatically filled when you tap an RFID card</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">Mobile Number *</label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="fas fa-user-plus me-2"></i>Register User
                                </button>
                                <button type="reset" class="btn btn-secondary ms-2">
                                    <i class="fas fa-redo me-2"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructions -->
    <section class="container mb-5">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Registration Instructions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-list-ol me-2"></i>Step-by-Step Process:</h6>
                        <ol>
                            <li>Tap an RFID card on the reader</li>
                            <li>The UID field will automatically populate</li>
                            <li>Fill in the user's personal information</li>
                            <li>Click "Register User" to complete registration</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes:</h6>
                        <ul>
                            <li>Each RFID card can only be registered once</li>
                            <li>All fields marked with * are required</li>
                            <li>Registered users can immediately access the door</li>
                            <li>You can manage users from the User Data section</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulate RFID reading (for testing purposes)
        function simulateRFID() {
            const uid = 'RFID_' + Math.random().toString(36).substr(2, 9).toUpperCase();
            document.getElementById('rfidInput').value = uid;
            document.getElementById('uid').value = uid;
            
            // Show success message
            showRFIDMessage('RFID card detected: ' + uid, 'success');
        }

        // Auto-fill UID when RFID input changes
        document.getElementById('rfidInput').addEventListener('input', function() {
            document.getElementById('uid').value = this.value;
        });

        // Show RFID messages
        function showRFIDMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type} alert-dismissible fade show`;
            messageDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.rfid-section');
            container.appendChild(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        // Listen for RFID data from ESP8266 (WebSocket or HTTP requests)
        function checkRFIDData() {
            // This would typically connect to ESP8266 via WebSocket or HTTP
            // For now, we'll simulate it
            console.log('Checking for RFID data...');
        }

        // Check for RFID data every second
        setInterval(checkRFIDData, 1000);

        // Add click event for simulate button
        document.addEventListener('DOMContentLoaded', function() {
            const simulateBtn = document.querySelector('.btn-simulate');
            if (simulateBtn) {
                simulateBtn.addEventListener('click', simulateRFID);
            }
        });
    </script>
</body>
</html>
