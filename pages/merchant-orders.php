<!-- <php
session_start();
include_once("../connection/dbcon.php");

// Check if the user is logged in and has merchant privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'merchant') {
    header('Location: ../index.php');
    exit();
}

$merchant_id = $_SESSION['user_id'];

// Add new product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    
    $query = "INSERT INTO products (merchant_id, product_name, price, stock_quantity) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isdi", $merchant_id, $product_name, $price, $stock_quantity);
    mysqli_stmt_execute($stmt);
}

// Update product
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    
    $query = "UPDATE products SET product_name = ?, price = ?, stock_quantity = ? WHERE id = ? AND merchant_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sdiii", $product_name, $price, $stock_quantity, $product_id, $merchant_id);
    mysqli_stmt_execute($stmt);
}

// Delete product
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    $query = "DELETE FROM products WHERE id = ? AND merchant_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $product_id, $merchant_id);
    mysqli_stmt_execute($stmt);
}

// Fetch merchant's products
$query = "SELECT id, product_name, price, stock_quantity FROM products WHERE merchant_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $merchant_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission for editing user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update_query = "UPDATE user SET username = ?, email = ?, role = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $user_id);
    mysqli_stmt_execute($stmt);

    // Redirect to refresh the page
    header('Location: admin-home.php');
    exit();
}
?> -->

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

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a7bc8;
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
        <h1 class="text-3xl font-bold mb-6">Manage Orders</h1>

        <div class="card">
            <div class="card-header">
                Recent Orders
            </div>
            <div class="p-4">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['username']); ?><br>
                                    <small><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                                <td>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="new_status" class="border rounded px-2 py-1">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary ml-2">Update</button>
                                    </form>
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary ml-2">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>

    </script>
</body>

</html>