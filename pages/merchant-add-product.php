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

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $created_at = date('Y-m-d H:i:s');  // Add this line to get the current timestamp

    // Handle file upload
    $target_dir = "../product_images/";
    $file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
    $file_name = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    $upload_ok = 1;

    // Check file size
    if ($_FILES["product_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $upload_ok = 0;
    }

    // Allow certain file formats
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    if ($upload_ok == 1) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now insert product details into the database
            $query = "INSERT INTO products (shop_id, product_name, category, price, stock, description, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "issdisss", $shop_id, $product_name, $category, $price, $stock, $description, $target_file, $created_at);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: merchant-products.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF7622;
            --text-color: #333;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .back-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 24px;
            text-decoration: none;
        }

        .back-icon:hover {
            color: #f0f0f0;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-align: center;
            display: inline-block;
            width: auto;
        }

        #btn {
            width: 100%;
        }

        .btn:hover {
            background-color: #e56a1f;
        }

        #image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 6px;
            display: none;
        }

        .full-width {
            grid-column: 1 / -1;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <a href="merchant-products.php" class="back-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Add New Product</h1>
            </div>
            <form action="merchant-add-product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="Fast Food">Fast Food</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" step="0.01" id="price" name="price" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" required>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="product_image">Product Image</label>
                    <div class="file-input-wrapper">
                        <button type="button" class="btn">Choose File</button>
                        <input type="file" id="product_image" name="product_image" accept="image/*">
                    </div>
                    <img id="image-preview" src="#" alt="Image preview" />
                </div>

                <div class="full-width">
                    <button type="submit" class="btn" id="btn">Add Product</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{$_SESSION['success_message']}',
                confirmButtonColor: '#3498db'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'merchant-products.php';
                }
            });
            ";
            unset($_SESSION['success_message']);
        }
        ?>

        // Image preview functionality
        document.getElementById('product_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        });
    </script>
</body>

</html>