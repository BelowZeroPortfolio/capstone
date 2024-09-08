<?php
session_start();
include_once("../connection/dbcon.php");

// Check if the user is logged in and has merchant privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'merchant') {
    header('Location: ../index.php');
    exit();
}

$merchant_id = $_SESSION['user_id'];

// Fetch the shop_id for the logged-in merchant
$shop_query = "SELECT shop_id FROM shops WHERE owner_id = ?";
$shop_stmt = mysqli_prepare($con, $shop_query);
mysqli_stmt_bind_param($shop_stmt, "i", $merchant_id);
mysqli_stmt_execute($shop_stmt);
$shop_result = mysqli_stmt_get_result($shop_stmt);

if ($shop_row = mysqli_fetch_assoc($shop_result)) {
    $shop_id = $shop_row['shop_id'];
} else {
    // Handle the case where the merchant doesn't have a shop
    echo "Error: You don't have a shop associated with your account.";
    exit();
}

// Handle delete action
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $delete_query = "DELETE FROM products WHERE product_id = ? AND shop_id = ?";
    $delete_stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $product_id, $shop_id);
    mysqli_stmt_execute($delete_stmt);
    
    // Redirect to refresh the page
    header('Location: merchant-products.php');
    exit();
}

// Handle file upload in the edit section
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Get the current image path
    $image_query = "SELECT image_path FROM products WHERE product_id = ?";
    $image_stmt = mysqli_prepare($con, $image_query);
    mysqli_stmt_bind_param($image_stmt, "i", $product_id);
    mysqli_stmt_execute($image_stmt);
    $image_result = mysqli_stmt_get_result($image_stmt);
    $current_image = mysqli_fetch_assoc($image_result)['image_path'];

    $image_path = $current_image; // Default to current image path

    // Check if a new file was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['size'] > 0) {
        $target_dir = "../product_images/"; // Updated to the correct path
        $file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $file_name = "product_" . $product_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        $upload_ok = 1;

        // Check file size (optional)
        if ($_FILES["product_image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $upload_ok = 0;
        }

        // Allow certain file formats (optional)
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $upload_ok = 0;
        }

        // Upload file
        if ($upload_ok == 1) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
                // Delete the old image if it exists
                if (!empty($current_image) && file_exists($current_image)) {
                    unlink($current_image);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        }
    }

    // Update the product details in the database
    $update_query = "UPDATE products SET product_name = ?, category = ?, price = ?, stock = ?, description = ?, image_path = ? WHERE product_id = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sdissi", $product_name, $category, $price, $stock, $description, $image_path, $product_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Redirect to refresh the page
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

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 2rem 0px;
        }

        .product-card {
            width: 100%;
            height: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            width: 100%;
            height: 180px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-info {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .product-description {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #FF7622;
        }

        .product-stock {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .action-icons {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.75rem;
        }

        .action-icons a, .action-icons button {
            color: #6c757d;
            font-size: 1.1em;
            margin-left: 1rem;
            transition: color 0.3s ease;
            background: none;
            border: none;
            cursor: pointer;
        }

        .action-icons a:hover, .action-icons button:hover {
            color: #343a40;
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

        .btn-secondary {
            background-color: #f8f9fa;
            color: #343a40;
            border: 1px solid #ced4da;
        }

        .btn-secondary:hover, .btn-secondary.active {
            background-color: #e9ecef;
            color: #212529;
        }

        .category-filter {
            transition: all 0.3s ease;
        }

        .category-filter.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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
            .product-grid{
                padding: 0px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
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

    <!-- Product List -->
    <div class="main-content">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Products</h1>
            <a href="merchant-add-product.php" class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                Add Product
            </a>
        </div>
        
        <!-- Category filter buttons -->
        <div class="mb-6">
            <button class="category-filter btn btn-secondary mr-2 mb-2" data-category="all">All</button>
            <button class="category-filter btn btn-secondary mr-2 mb-2" data-category="Fast Food">Fast Food</button>
            <button class="category-filter btn btn-secondary mr-2 mb-2" data-category="Drinks">Drinks</button>
            <button class="category-filter btn btn-secondary mr-2 mb-2" data-category="Desserts">Desserts</button>
            <button class="category-filter btn btn-secondary mr-2 mb-2" data-category="Snacks">Snacks</button>
        </div>

        <?php
        // Fetch products from the database
        $query = "SELECT * FROM products WHERE shop_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $shop_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="product-grid">';
            echo '<p id="no-products" class="text-center text-gray-600 w-full hidden">No products found in this category.</p>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="product-card" data-category="' . htmlspecialchars($row['category']) . '">';
                echo '<div class="product-image-container">';
                if (!empty($row['image_path'])) {
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" class="product-image">';
                } else {
                    echo '<img src="../image/default_product_image.png" alt="Default Product Image" class="product-image">';
                }
                echo '</div>';
                echo '<div class="product-info">';
                echo '<p class="product-category">' . htmlspecialchars($row['category']) . '</p>';
                echo '<h2 class="product-name">' . htmlspecialchars($row['product_name']) . '</h2>';
                echo '<p class="product-description">' . htmlspecialchars(substr($row['description'], 0, 100)) . '...</p>';
                echo '<div class="product-meta">';
                echo '<span class="product-price">₱' . number_format($row['price'], 2) . '</span>';
                echo '<span class="product-stock">Stock: ' . $row['stock'] . '</span>';
                echo '</div>';
                echo '<div class="action-icons">';
                echo '<a href="merchant-edit-product.php?id=' . $row['product_id'] . '" title="Edit"><i class="fas fa-edit"></i></a>';
                echo '<form id="delete-form-' . $row['product_id'] . '" method="POST" style="display: inline;">';
                echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                echo '<input type="hidden" name="delete_product" value="1">';
                echo '<button type="button" onclick="confirmDelete(' . $row['product_id'] . ', \'' . htmlspecialchars($row['product_name'], ENT_QUOTES) . '\')" title="Delete"><i class="fas fa-trash-alt"></i></button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p class="text-center text-gray-600">No products found. Add some products to get started!</p>';
        }
        ?>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Product</h3>
                <form id="editProductForm" action="" method="POST" class="mt-2">
                    <input type="hidden" id="edit_product_id" name="product_id">
                    <input type="text" id="edit_product_name" name="product_name" placeholder="Product Name" required class="w-full p-2 border rounded mb-2">
                    <input type="number" id="edit_price" name="price" placeholder="Price" step="0.01" required class="w-full p-2 border rounded mb-2">
                    <input type="number" id="edit_stock_quantity" name="stock_quantity" placeholder="Stock Quantity" required class="w-full p-2 border rounded mb-2">
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                </form>
                <button onclick="closeEditModal()" class="btn btn-secondary mt-2">Close</button>
            </div>
        </div>
    </div>

    <script>
        function editProduct(productId, productName, price, stockQuantity) {
            document.getElementById('edit_product_id').value = productId;
            document.getElementById('edit_product_name').value = productName;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock_quantity').value = stockQuantity;
            document.getElementById('editProductModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editProductModal').classList.add('hidden');
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('editProductModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.category-filter');
            const productCards = document.querySelectorAll('.product-card');
            const noProductsMessage = document.getElementById('no-products');

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const category = button.getAttribute('data-category');
                    let visibleProducts = 0;
                    
                    productCards.forEach(card => {
                        if (category === 'all' || card.getAttribute('data-category') === category) {
                            card.style.display = 'flex';
                            visibleProducts++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Show/hide "No products found" message
                    if (visibleProducts === 0) {
                        noProductsMessage.classList.remove('hidden');
                    } else {
                        noProductsMessage.classList.add('hidden');
                    }

                    // Update active button style
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            });
        });

        function confirmDelete(productId, productName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete the product "${productName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            });
        }
    </script>
</body>

</html>