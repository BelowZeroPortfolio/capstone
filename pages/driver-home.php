<?php
session_start();
include_once("../connection/dbcon.php");

// Check if user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
    header("Location: ../index.php");
    exit();
}

// Fetch driver information
$user_id = $_SESSION['user_id'];
$query = "SELECT u.*, d.vehicle_number, d.phone_number, d.status 
          FROM users u 
          JOIN drivers d ON u.user_id = d.user_id 
          WHERE u.user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar .active {
            background-color: var(--sidebar-hover);
            border-left: 4px solid var(--primary-color);
        }

        .sidebar a {
            color: #ecf0f1;
            padding: 16px 24px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: var(--sidebar-hover);
            padding-left: 30px;
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-weight: bold;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            border-radius: 9999px;
        }

        #sidebarToggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            #sidebarToggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1000;
                background-color: var(--primary-color);
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
            }
        }
    </style>
</head>
<body>
    <button id="sidebarToggle" class="btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar flex flex-col h-full">
        <div class="flex-grow">
            <a href="#" class="text-xl font-bold px-4 py-6">
                <i class="fas fa-truck"></i> Driver Panel
            </a>
            <hr>
            <a href="driver-home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'driver-home.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="driver-deliveries.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'driver-deliveries.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Deliveries
            </a>
            <a href="driver-earnings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'driver-earnings.php' ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i> Earnings
            </a>
        </div>
        <div class="mt-auto">
            <a href="driver-settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'driver-settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
            <hr>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1 class="text-3xl font-bold mb-6">Driver Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Driver Profile</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($driver['email']); ?></p>
                <p><strong>Vehicle Number:</strong> <?php echo htmlspecialchars($driver['vehicle_number']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($driver['phone_number']); ?></p>
                <p><strong>Status:</strong> 
                    <span class="status-badge bg-<?php echo $driver['status'] == 'active' ? 'green' : 'yellow'; ?>-500 text-white">
                        <?php echo ucfirst($driver['status']); ?>
                    </span>
                </p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Deliveries</h2>
                <?php if (empty($recent_deliveries)): ?>
                    <p>No recent deliveries.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Order ID</th>
                                    <th class="px-4 py-2 text-left">Customer</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_deliveries as $delivery): ?>
                                    <tr>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($delivery['order_id']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($delivery['customer_name']); ?></td>
                                        <td class="px-4 py-2">
                                            <span class="status-badge bg-<?php echo $delivery['status'] == 'completed' ? 'green' : 'blue'; ?>-500 text-white">
                                                <?php echo ucfirst($delivery['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Earnings Overview</h2>
                <!-- Add a chart or summary of earnings here -->
                <p>Earnings information will be displayed here.</p>
            </div>
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Upcoming Deliveries</h2>
                <!-- Add a list or calendar of upcoming deliveries -->
                <p>Upcoming delivery information will be displayed here.</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
