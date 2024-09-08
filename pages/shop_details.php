<?php
include_once("../connection/dbcon.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Check if shop_id is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$shop_id = mysqli_real_escape_string($con, $_GET['id']);

// Fetch shop details
$shop_query = "SELECT * FROM shops WHERE shop_id = '$shop_id'";
$shop_result = mysqli_query($con, $shop_query);

if (mysqli_num_rows($shop_result) == 0) {
    header("Location: shop.php");
    exit;
}

$shop = mysqli_fetch_assoc($shop_result);

// Fetch products for this shop
$products_query = "SELECT * FROM products WHERE shop_id = '$shop_id'";
$products_result = mysqli_query($con, $products_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shop['shop_name']); ?> - BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_sho.css">
    <style>
        .shop-details {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Include your header here -->

    <div class="container mt-4">
        <div class="shop-details">
            <h1><?php echo htmlspecialchars($shop['shop_name']); ?></h1>
            <p>Rating: <?php echo number_format($shop['rating'], 1); ?> &#11088;</p>
            <!-- Add more shop details as needed -->
        </div>

        <h2>Products</h2>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($products_result)) : ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>Price: ₱<?php echo number_format($product['price'], 2); ?></p>
                    <!-- Add more product details or a "Add to Cart" button here -->
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Include your footer here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
