<?php
session_start();
// Database connection
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "iot-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login functionality
$loginError = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // In a real application, you should use prepared statements and password_hash/password_verify
    // This is a simplified example
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
    } else {
        $loginError = 'Invalid credentials';
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Function to handle form submissions for different tables
function handleFormSubmission($conn)
{
    // Add new notice
    if (isset($_POST['add_notice'])) {
        $notice = $_POST['notice'];
        $notice_by = $_POST['notice_by'];

        $sql = "INSERT INTO notices (notice, notice_by) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $notice, $notice_by);

        if ($stmt->execute()) {
            return "Notice added successfully!";
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Add new RFID
    if (isset($_POST['add_rfid'])) {
        $rfid = $_POST['rfid'];

        $sql = "INSERT INTO access_granted_rfids (rfid) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $rfid);

        if ($stmt->execute()) {
            return "RFID added successfully!";
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Update timetable
    if (isset($_POST['update_timetable'])) {
        $id = $_POST['id'];
        $today = $_POST['today'];
        $subjects = [];

        for ($i = 1; $i <= 8; $i++) {
            $subjects[] = $_POST['sub' . $i];
        }

        $sql = "UPDATE timetable SET today = ?, sub1 = ?, sub2 = ?, sub3 = ?, sub4 = ?, sub5 = ?, sub6 = ?, sub7 = ?, sub8 = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $today, $subjects[0], $subjects[1], $subjects[2], $subjects[3], $subjects[4], $subjects[5], $subjects[6], $subjects[7], $id);

        if ($stmt->execute()) {
            return "Timetable updated successfully!";
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Add new timetable entry
    if (isset($_POST['add_timetable'])) {
        $today = $_POST['today'];
        $subjects = [];

        for ($i = 1; $i <= 8; $i++) {
            $subjects[] = $_POST['sub' . $i];
        }

        $sql = "INSERT INTO timetable (today, sub1, sub2, sub3, sub4, sub5, sub6, sub7, sub8) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $today, $subjects[0], $subjects[1], $subjects[2], $subjects[3], $subjects[4], $subjects[5], $subjects[6], $subjects[7]);

        if ($stmt->execute()) {
            return "Timetable added successfully!";
        } else {
            return "Error: " . $stmt->error;
        }
    }

    // Delete record functionality
    if (isset($_GET['delete'])) {
        $table = $_GET['table'];
        $id = $_GET['id'];

        $allowed_tables = ['notices', 'access_granted_rfids', 'timetable', 'DHT22Readings', 'rfid_data'];

        if (in_array($table, $allowed_tables)) {
            $sql = "DELETE FROM $table WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                return "Record deleted successfully!";
            } else {
                return "Error: " . $stmt->error;
            }
        }
    }

    return "";
}

// Handle form submissions
$message = handleFormSubmission($conn);

// Get the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .8);
            padding: 1rem;
            border-radius: 0.25rem;
            margin: 0.2rem 0;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, .2);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
        }

        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
        }

        .card {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1.5rem 0 rgba(0, 0, 0, 0.3);
        }

        .dashboard-card {
            transition: transform 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .table-container {
            overflow-x: auto;
        }
    </style>
</head>

<body>

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <!-- Login Form -->
        <div class="login-container">
            <div class="login-card">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h4 class="text-primary text-center mb-0">IoT Admin Panel</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($loginError)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $loginError; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Main Admin Panel Layout -->
        <div class="d-flex">
            <!-- Sidebar -->
            <div class="sidebar col-md-2 px-0 position-fixed h-100">
                <div class="text-center py-4 mb-3">
                    <h4 class="text-white">IoT Admin Panel</h4>
                </div>

                <div class="px-3">
                    <div class="nav flex-column">
                        <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="?page=notices" class="nav-link <?php echo $page === 'notices' ? 'active' : ''; ?>">
                            <i class="fas fa-bullhorn"></i> Notices
                        </a>
                        <a href="?page=rfid" class="nav-link <?php echo $page === 'rfid' ? 'active' : ''; ?>">
                            <i class="fas fa-id-card"></i> RFID Management
                        </a>
                        <a href="?page=timetable" class="nav-link <?php echo $page === 'timetable' ? 'active' : ''; ?>">
                            <i class="fas fa-calendar-alt"></i> Timetable
                        </a>
                        <a href="?page=sensors" class="nav-link <?php echo $page === 'sensors' ? 'active' : ''; ?>">
                            <i class="fas fa-thermometer-half"></i> Sensor Data
                        </a>
                        <a href="?logout=1" class="nav-link text-danger mt-4">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 ms-auto px-4">
                <!-- Topbar -->
                <div class="topbar d-flex justify-content-between align-items-center bg-white mb-4 px-3">
                    <div>
                        <h5 class="text-gray-800 mb-0">
                            <?php
                            switch ($page) {
                                case 'dashboard':
                                    echo "Dashboard";
                                    break;
                                case 'notices':
                                    echo "Notice Management";
                                    break;
                                case 'rfid':
                                    echo "RFID Management";
                                    break;
                                case 'timetable':
                                    echo "Timetable Management";
                                    break;
                                case 'sensors':
                                    echo "Sensor Data";
                                    break;
                                default:
                                    echo "Dashboard";
                            }
                            ?>
                        </h5>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2">Welcome, <?php echo $_SESSION['username']; ?></span>
                        <a href="?logout=1" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>

                <!-- Message Display -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Page Content -->
                <?php
                // Include the appropriate page content based on the page parameter
                switch ($page) {
                    case 'dashboard':
                        include 'admin/dashboard.php';
                        break;
                    case 'notices':
                        include 'admin/notices.php';
                        break;
                    case 'rfid':
                        include 'admin/rfid.php';
                        break;
                    case 'timetable':
                        include 'admin/timetable.php';
                        break;
                    case 'sensors':
                        include 'admin/sensors.php';
                        break;
                    default:
                        include 'admin/dashboard.php';
                }
                ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto dismiss alerts after 5 seconds
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

</body>

</html>
