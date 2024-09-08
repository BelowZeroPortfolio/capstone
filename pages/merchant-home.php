<?php
session_start();
include_once("../connection/dbcon.php");

// Check if the user is logged in and has merchant privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'merchant') {
    header('Location: ../index.php');
    exit();
}

$merchant_id = $_SESSION['user_id'];

// Fetch merchant and shop information
$query = "SELECT u.*, s.* FROM users u 
          LEFT JOIN shops s ON u.user_id = s.owner_id 
          WHERE u.user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $merchant_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$merchant_info = mysqli_fetch_assoc($result);

// Fetch total number of products
$product_query = "SELECT COUNT(*) as total_products FROM products WHERE shop_id = ?";
$product_stmt = mysqli_prepare($con, $product_query);
mysqli_stmt_bind_param($product_stmt, "i", $merchant_info['shop_id']);
mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);
$total_products = mysqli_fetch_assoc($product_result)['total_products'];

// Fetch total number of orders (you may need to adjust this based on your database structure)
$order_query = "SELECT COUNT(*) as total_orders FROM orders WHERE shop_id = ?";
$order_stmt = mysqli_prepare($con, $order_query);
mysqli_stmt_bind_param($order_stmt, "i", $merchant_info['shop_id']);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$total_orders = mysqli_fetch_assoc($order_result)['total_orders'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF7622;
            --secondary-color: #FFA366;
            --background-color: #f5f7fa;
            --text-color: #333;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
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
            background-color: var(--background-color);
            min-height: 100vh;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            font-weight: bold;
            font-size: 1.2rem;
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
                </i> Merchant Panel
            </a>
            <hr>
            <a href="merchant-home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'merchant-home.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-table"></i> Dashboard
            </a>
            <a href="merchant-products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'merchant-products.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Products
            </a>
            <a href="merchant-orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'merchant-orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
        </div>
        <div class="mt-auto">
            <a href="merchant-settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'merchant-settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
            <hr>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1 class="text-3xl font-bold mb-6">Welcome, <?php echo htmlspecialchars($merchant_info['first_name'] . ' ' . $merchant_info['last_name']); ?>!</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card">
                <div class="card-header">Shop Information</div>
                <div class="p-4">
                    <p><strong>Shop Name:</strong> <?php echo htmlspecialchars($merchant_info['shop_name']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($merchant_info['address']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($merchant_info['phone_number']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($merchant_info['email']); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Products</div>
                <div class="p-4">
                    <p class="text-4xl font-bold"><?php echo $total_products; ?></p>
                    <p>Total Products</p>
                    <a href="merchant-products.php" class="text-blue-500 hover:underline mt-2 inline-block">Manage Products</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Orders</div>
                <div class="p-4">
                    <p class="text-4xl font-bold"><?php echo $total_orders; ?></p>
                    <p>Total Orders</p>
                    <a href="merchant-orders.php" class="text-blue-500 hover:underline mt-2 inline-block">View Orders</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Recent Activity</div>
            <div class="p-4">
                <!-- Add recent activity content here -->
                <p>No recent activity to display.</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>