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

// Handle history deletion
if (isset($_POST['delete_history'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM access_history WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch access history with user details
$result = $conn->query("
    SELECT h.*, u.name, u.gender, u.email, u.mobile 
    FROM access_history h 
    LEFT JOIN users u ON h.uid = u.uid 
    ORDER BY h.access_time DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access History - Smart Door Lock System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        .history-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .history-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .status-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .status-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn-action {
            margin: 2px;
        }
        .user-info {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
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
                        <a class="nav-link" href="read_tag.php">
                            <i class="fas fa-id-card me-1"></i>Read Tag ID
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="history.php">
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
            <h1 class="display-5 fw-bold">Access History</h1>
            <p class="lead">Track all door access attempts and user activities</p>
        </div>
    </section>

    <!-- Statistics Cards -->
    <section class="container mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-door-open fa-2x mb-3"></i>
                    <div class="stats-number" id="totalAccess">0</div>
                    <div>Total Access</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-check-circle fa-2x mb-3"></i>
                    <div class="stats-number" id="successfulAccess">0</div>
                    <div>Successful</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-times-circle fa-2x mb-3"></i>
                    <div class="stats-number" id="failedAccess">0</div>
                    <div>Failed</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-users fa-2x mb-3"></i>
                    <div class="stats-number" id="uniqueUsers">0</div>
                    <div>Unique Users</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Access History Table -->
    <section class="container mb-5">
        <div class="card history-card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Access Logs
                </h5>
                <button class="btn btn-light btn-sm" onclick="exportHistory()">
                    <i class="fas fa-download me-1"></i>Export
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="historyTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Date & Time</th>
                                <th>RFID UID</th>
                                <th>User Details</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong><?php echo date('M d, Y', strtotime($row['access_time'])); ?></strong>
                                        <small class="text-muted"><?php echo date('H:i:s', strtotime($row['access_time'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($row['uid']); ?></span>
                                </td>
                                <td>
                                    <?php if ($row['name']): ?>
                                    <div class="user-info">
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div class="text-muted">
                                            <small>
                                                <i class="fas fa-venus-mars me-1"></i><?php echo htmlspecialchars($row['gender']); ?> |
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($row['email']); ?> |
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($row['mobile']); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Unregistered Card
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['access_granted']): ?>
                                    <span class="badge status-success status-badge">
                                        <i class="fas fa-check me-1"></i>Granted
                                    </span>
                                    <?php else: ?>
                                    <span class="badge status-danger status-badge">
                                        <i class="fas fa-times me-1"></i>Denied
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-action" onclick="viewDetails(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-action" onclick="deleteHistory(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Access Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                order: [[0, 'desc']], // Sort by date/time
                pageLength: 25,
                responsive: true
            });
            
            updateStats();
        });

        function updateStats() {
            // Calculate statistics from table data
            const table = $('#historyTable').DataTable();
            const data = table.data().toArray();
            
            let total = data.length;
            let successful = 0;
            let failed = 0;
            let uniqueUsers = new Set();
            
            data.forEach(row => {
                const status = $(row[3]).text().trim();
                if (status.includes('Granted')) {
                    successful++;
                } else {
                    failed++;
                }
                
                const uid = $(row[1]).text().trim();
                if (uid) uniqueUsers.add(uid);
            });
            
            document.getElementById('totalAccess').textContent = total;
            document.getElementById('successfulAccess').textContent = successful;
            document.getElementById('failedAccess').textContent = failed;
            document.getElementById('uniqueUsers').textContent = uniqueUsers.size;
        }

        function viewDetails(id) {
            // Fetch and display detailed information
            fetch('get_history_details.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    const content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Access Information</h6>
                                <p><strong>Date:</strong> ${data.access_time}</p>
                                <p><strong>RFID UID:</strong> <span class="badge bg-info">${data.uid}</span></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge ${data.access_granted ? 'bg-success' : 'bg-danger'}">
                                        ${data.access_granted ? 'Granted' : 'Denied'}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>User Information</h6>
                                ${data.name ? `
                                    <p><strong>Name:</strong> ${data.name}</p>
                                    <p><strong>Gender:</strong> ${data.gender}</p>
                                    <p><strong>Email:</strong> ${data.email}</p>
                                    <p><strong>Mobile:</strong> ${data.mobile}</p>
                                ` : '<p class="text-danger">Unregistered RFID card</p>'}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('detailsContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('detailsModal')).show();
                })
                .catch(error => {
                    console.error('Error fetching details:', error);
                    alert('Error fetching access details');
                });
        }

        function deleteHistory(id) {
            if (confirm('Are you sure you want to delete this access record?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'history.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_history';
                input.value = '1';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                form.appendChild(input);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function exportHistory() {
            // Export history data to CSV
            const table = $('#historyTable').DataTable();
            const data = table.data().toArray();
            
            let csv = 'Date,Time,RFID UID,User Name,Status\n';
            
            data.forEach(row => {
                const date = $(row[0]).find('strong').text();
                const time = $(row[0]).find('small').text();
                const uid = $(row[1]).text().trim();
                const userName = $(row[2]).find('.fw-bold').text() || 'Unregistered';
                const status = $(row[3]).text().trim();
                
                csv += `"${date}","${time}","${uid}","${userName}","${status}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'door_access_history.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
