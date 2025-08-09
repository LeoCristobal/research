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

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $uid = $_POST['uid'];
    $stmt = $conn->prepare("DELETE FROM users WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $stmt->close();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data - Smart Door Lock System</title>
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
        .user-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .user-card:hover {
            transform: translateY(-2px);
        }
        .btn-action {
            margin: 2px;
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
                        <a class="nav-link active" href="users.php">
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-5 fw-bold">User Data Management</h1>
            <p class="lead">Manage registered users and their RFID access permissions</p>
        </div>
    </section>

    <!-- User Data Table -->
    <section class="container mb-5">
        <div class="card user-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Registered Users
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>RFID UID</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($user['uid']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user['gender'] == 'Male' ? 'primary' : 'danger'; ?>">
                                        <?php echo htmlspecialchars($user['gender']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-action" onclick="editUser('<?php echo $user['uid']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-action" onclick="deleteUser('<?php echo $user['uid']; ?>', '<?php echo htmlspecialchars($user['name']); ?>')">
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" action="update_user.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="editUid" name="uid">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editGender" class="form-label">Gender</label>
                            <select class="form-select" id="editGender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMobile" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="editMobile" name="mobile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                order: [[5, 'desc']], // Sort by registration date
                pageLength: 10,
                responsive: true
            });
        });

        function editUser(uid) {
            // Fetch user data and populate modal
            fetch('get_user.php?uid=' + uid)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('editUid').value = user.uid;
                    document.getElementById('editName').value = user.name;
                    document.getElementById('editGender').value = user.gender;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editMobile').value = user.mobile;
                    
                    new bootstrap.Modal(document.getElementById('editUserModal')).show();
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                    alert('Error fetching user data');
                });
        }

        function deleteUser(uid, name) {
            if (confirm('Are you sure you want to delete user "' + name + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'users.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_user';
                input.value = '1';
                
                const uidInput = document.createElement('input');
                uidInput.type = 'hidden';
                uidInput.name = 'uid';
                uidInput.value = uid;
                
                form.appendChild(input);
                form.appendChild(uidInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
