<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['wifi_name'])) {
    header('Location: index.php');
    exit();
}

// Function to get server IP address
function getServerIP() {
    // Try to get the local IP address automatically
    if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] != '::1' && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
        return $_SERVER['SERVER_ADDR'];
    }
    
    // Fallback: try to get local IP from network interfaces
    $localIP = '192.168.1.100'; // Default fallback
    
    if (function_exists('shell_exec')) {
        // Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig | findstr "IPv4"');
            if (preg_match('/\d+\.\d+\.\d+\.\d+/', $output, $matches)) {
                $localIP = $matches[0];
            }
        }
        // Linux/Mac
        else {
            $output = shell_exec('hostname -I 2>/dev/null');
            if (preg_match('/\d+\.\d+\.\d+\.\d+/', $output, $matches)) {
                $localIP = trim($matches[0]);
            }
        }
    }
    
    return $localIP;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Door Lock System - Dashboard</title>
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
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .status-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
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
                        <a class="nav-link active" href="dashboard.php">
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Welcome to Smart Door Lock System</h1>
            <p class="lead mb-4">Secure your door with RFID technology and IoT connectivity</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="status-card">
                        <h4><i class="fas fa-wifi me-2"></i>WiFi Connected</h4>
                        <p class="mb-0">Network: <?php echo htmlspecialchars($_SESSION['wifi_name']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="status-card">
                        <h4><i class="fas fa-door-open me-2"></i>Door Status</h4>
                        <p class="mb-0" id="doorStatus">Checking...</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="status-card">
                        <h4><i class="fas fa-users me-2"></i>Registered Users</h4>
                        <p class="mb-0" id="userCount">Loading...</p>
                    </div>
                </div>
            </div>
            
            
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="container mb-5">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h5 class="card-title">1. RFID Authentication</h5>
                        <p class="card-text">Users tap their RFID card on the reader. The system validates the card and checks if the user is authorized.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h5 class="card-title">2. Processing</h5>
                        <p class="card-text">The ESP8266 processes the RFID data and communicates with the web server to verify user permissions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h5 class="card-title">3. Door Control</h5>
                        <p class="card-text">If authorized, the servo motor unlocks the door and the LCD displays "Door Open". Unauthorized cards show "Invalid Card".</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Components -->
    <section class="container mb-5">
        <h2 class="text-center mb-5">System Components</h2>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h6 class="card-title">NodeMCU ESP8266</h6>
                        <p class="card-text">WiFi-enabled microcontroller for IoT connectivity</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h6 class="card-title">RFID Reader</h6>
                        <p class="card-text">Reads RFID cards and transmits UID data</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h6 class="card-title">Servo Motor</h6>
                        <p class="card-text">Controls door lock mechanism</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card feature-card text-center h-100">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-tv"></i>
                        </div>
                        <h6 class="card-title">I2C LCD Display</h6>
                        <p class="card-text">Shows door status and messages</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update door status and user count
        function updateStatus() {
            // Simulate real-time updates
            fetch('get_status.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('doorStatus').textContent = data.door_status;
                    document.getElementById('userCount').textContent = data.user_count + ' users';
                })
                .catch(error => {
                    console.error('Error fetching status:', error);
                });
        }

        // Update status every 5 seconds
        setInterval(updateStatus, 5000);
        updateStatus(); // Initial update
    </script>
</body>
</html>
