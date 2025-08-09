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

$user_data = null;
$message = '';
$message_type = '';

// Handle RFID UID lookup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['uid'])) {
    $uid = $_POST['uid'];
    
    if (!empty($uid)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $message = "User found! Access granted.";
            $message_type = "success";
        } else {
            $message = "RFID card not registered. Access denied.";
            $message_type = "danger";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Tag ID - Smart Door Lock System</title>
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
        .rfid-reader-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }
        .rfid-input {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
        }
        .rfid-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .user-details-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-granted {
            background-color: #28a745;
        }
        .status-denied {
            background-color: #dc3545;
        }
        .btn-read {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-read:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
        .animation-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
                        <a class="nav-link" href="registration.php">
                            <i class="fas fa-user-plus me-1"></i>Registration
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="read_tag.php">
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
            <h1 class="display-5 fw-bold">Read Tag ID</h1>
            <p class="lead">Tap RFID cards to read user information and verify access</p>
        </div>
    </section>

    <!-- RFID Reader Section -->
    <section class="container mb-4">
        <div class="rfid-reader-section">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h2 class="mb-4">
                        <i class="fas fa-id-card me-2"></i>RFID Card Reader
                    </h2>
                    <p class="lead mb-4">Place an RFID card near the reader to automatically detect and read the UID</p>
                    
                    <form method="POST" action="" id="rfidForm">
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-white text-dark">
                                <i class="fas fa-microchip fa-2x"></i>
                            </span>
                            <input type="text" class="form-control rfid-input" id="rfidInput" name="uid" 
                                   placeholder="Waiting for RFID card..." readonly>
                            <button class="btn btn-read" type="button" onclick="simulateRFID()">
                                <i class="fas fa-sim-card me-2"></i>Simulate Card
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-read" id="readBtn" disabled>
                                <i class="fas fa-search me-2"></i>Read Tag Details
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <small class="text-white-50">
                            <i class="fas fa-info-circle me-1"></i>
                            The system will automatically detect RFID cards and display user information
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Details Display -->
    <?php if ($user_data || $message): ?>
    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <span class="status-indicator status-<?php echo $message_type == 'success' ? 'granted' : 'denied'; ?>"></span>
                        <strong class="me-2">
                            <?php echo $message_type == 'success' ? 'Access Granted' : 'Access Denied'; ?>
                        </strong>
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($user_data): ?>
                <div class="card user-details-card">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-check me-2"></i>User Information
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Full Name</label>
                                    <p class="form-control-plaintext fs-5"><?php echo htmlspecialchars($user_data['name']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">RFID UID</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info fs-6"><?php echo htmlspecialchars($user_data['uid']); ?></span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Gender</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-<?php echo $user_data['gender'] == 'Male' ? 'primary' : 'danger'; ?> fs-6">
                                            <?php echo htmlspecialchars($user_data['gender']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Email Address</label>
                                    <p class="form-control-plaintext fs-6"><?php echo htmlspecialchars($user_data['email']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Mobile Number</label>
                                    <p class="form-control-plaintext fs-6"><?php echo htmlspecialchars($user_data['mobile']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Registration Date</label>
                                    <p class="form-control-plaintext fs-6"><?php echo date('M d, Y H:i', strtotime($user_data['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <div class="alert alert-success">
                                <i class="fas fa-door-open me-2"></i>
                                <strong>Door Access: GRANTED</strong>
                                <br>
                                <small>This user is authorized to access the door</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Instructions -->
    <section class="container mb-5">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>How to Use
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-list-ol me-2"></i>Step-by-Step Process:</h6>
                        <ol>
                            <li>Place an RFID card near the reader</li>
                            <li>The UID will automatically appear in the input field</li>
                            <li>Click "Read Tag Details" to verify the user</li>
                            <li>View user information and access status</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-lightbulb me-2"></i>Use Cases:</h6>
                        <ul>
                            <li>Verify user identity before granting access</li>
                            <li>Check if an RFID card is registered</li>
                            <li>View user details for security purposes</li>
                            <li>Test RFID card functionality</li>
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
            document.getElementById('readBtn').disabled = false;
            document.getElementById('readBtn').classList.add('animation-pulse');
            
            // Show success message
            showRFIDMessage('RFID card detected: ' + uid, 'success');
        }

        // Show RFID messages
        function showRFIDMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type} alert-dismissible fade show`;
            messageDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.rfid-reader-section');
            container.appendChild(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        // Auto-enable read button when RFID input has value
        document.getElementById('rfidInput').addEventListener('input', function() {
            const readBtn = document.getElementById('readBtn');
            if (this.value.trim() !== '') {
                readBtn.disabled = false;
                readBtn.classList.add('animation-pulse');
            } else {
                readBtn.disabled = true;
                readBtn.classList.remove('animation-pulse');
            }
        });

        // Listen for RFID data from ESP8266
        function checkRFIDData() {
            // This would typically connect to ESP8266 via WebSocket or HTTP
            // For now, we'll simulate it
            console.log('Checking for RFID data...');
        }

        // Check for RFID data every second
        setInterval(checkRFIDData, 1000);

        // Auto-submit form when RFID is detected (simulation)
        document.getElementById('rfidInput').addEventListener('change', function() {
            if (this.value.trim() !== '') {
                setTimeout(() => {
                    document.getElementById('rfidForm').submit();
                }, 1000); // Simulate processing time
            }
        });

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
