<?php
session_start();
include_once("../connection/dbcon.php");

// Check if the user is logged in and has merchant privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'merchant') {
    header('Location: ../index.php');
    exit();
}

$merchant_id = $_SESSION['user_id'];

// Check if a product ID is provided
if (!isset($_GET['id'])) {
    header('Location: merchant-products.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch the product details
$query = "SELECT p.*, s.owner_id FROM products p JOIN shops s ON p.shop_id = s.shop_id WHERE p.product_id = ? AND s.owner_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $product_id, $merchant_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: merchant-products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    // Handle file upload
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == 0) {
        $upload_dir = '../product_images/';
        $file_extension = pathinfo($_FILES["image_path"]["name"], PATHINFO_EXTENSION);
        $file_name = "product_" . $product_id . "_" . time() . "." . $file_extension;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $file_path)) {
            $image_path = $file_path;
            // Delete the old image if it exists
            if (!empty($product['image_path']) && file_exists($product['image_path'])) {
                unlink($product['image_path']);
            }
        } else {
            echo "Failed to upload the image.";
            exit();
        }
    } else {
        // No new image uploaded, keep the existing image path
        $image_path = $product['image_path'];
    }

    // Update the product in the database
    $update_query = "UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?, image_path = ?, category = ? WHERE product_id = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssddssi", $product_name, $description, $price, $stock, $image_path, $category, $product_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        header('Location: merchant-products.php');
        exit();
    } else {
        echo "Error updating product: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6">Edit Product</h1>
        
        <?php if ($product): ?>
        <form action="merchant-edit-product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="product_name">
                    Product Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="product_name" type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                    Price
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="price" type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="stock">
                    Stock
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="stock" type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="image_path">
                    Product Image
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="image_path" type="file" name="image_path" accept="image/*">
            </div>
            <?php if (!empty($product['image_path'])): ?>
                <div class="mb-4">
                    <p class="text-gray-700 text-sm font-bold mb-2">Current image:</p>
                    <div class="w-40 bg-white shadow rounded-lg overflow-hidden">
                        <div class="h-40 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current product image" class="w-full h-full object-cover">
                        </div>
                        <div class="p-2">
                            <h4 class="text-sm font-semibold truncate"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                            <p class="text-xs text-gray-600">₱<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-gray-700 text-sm mb-4">No current image</p>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                    Category
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="Fast Food" <?php echo ($product['category'] == 'Fast Food') ? 'selected' : ''; ?>>Fast Food</option>
                    <option value="Drinks" <?php echo ($product['category'] == 'Drinks') ? 'selected' : ''; ?>>Drinks</option>
                    <option value="Desserts" <?php echo ($product['category'] == 'Desserts') ? 'selected' : ''; ?>>Desserts</option>
                    <option value="Snacks" <?php echo ($product['category'] == 'Snacks') ? 'selected' : ''; ?>>Snacks</option>
                </select>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Product
                </button>
                <a href="merchant-products.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
        <?php else: ?>
            <p class="text-red-500">Product not found or you don't have permission to edit it.</p>
        <?php endif; ?>
    </div>
</body>
</html>