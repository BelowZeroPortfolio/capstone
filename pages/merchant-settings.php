<?php
session_start();
include_once("../connection/dbcon.php");

// Check if the user is logged in and has merchant privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'merchant') {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Add this new section to handle image uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $upload_dir = "../path/";
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $upload_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update the database with the new image path
            $image_type = $_POST['image_type'];
            if ($image_type === 'profile_picture') {
                $update_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            } else if ($image_type === 'logo') {
                $update_query = "UPDATE shops SET logo = ? WHERE owner_id = ?";
            }
            $update_stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($update_stmt, "si", $target_file, $user_id);
            mysqli_stmt_execute($update_stmt);

            // Also update the other table to keep them in sync
            if ($image_type === 'profile_picture') {
                $sync_query = "UPDATE shops SET logo = ? WHERE owner_id = ?";
            } else if ($image_type === 'logo') {
                $sync_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            }
            $sync_stmt = mysqli_prepare($con, $sync_query);
            mysqli_stmt_bind_param($sync_stmt, "si", $target_file, $user_id);
            mysqli_stmt_execute($sync_stmt);

            echo json_encode(['success' => true, 'file' => $target_file]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File is not an image.']);
    }
    exit;
}

// Fetch user's profile including image paths
$user_query = "SELECT u.first_name, u.last_name, u.username, u.email, u.birthday, u.profile_picture, s.logo as shop_logo
               FROM users u
               LEFT JOIN shops s ON u.user_id = s.owner_id
               WHERE u.user_id = ?";
$user_stmt = mysqli_prepare($con, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_profile = mysqli_fetch_assoc($user_result);

// Fetch shop profile
$shop_query = "SELECT * FROM shops WHERE owner_id = ?";
$shop_stmt = mysqli_prepare($con, $shop_query);
mysqli_stmt_bind_param($shop_stmt, "i", $user_id);
mysqli_stmt_execute($shop_stmt);
$shop_result = mysqli_stmt_get_result($shop_stmt);
$shop_profile = mysqli_fetch_assoc($shop_result);

$is_new_merchant = empty($shop_profile);

// Handle form submission for updating or creating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $shop_name = $_POST['shop_name'];
    $shop_address = $_POST['shop_address'];
    $shop_phone = $_POST['shop_phone'];
    $shop_email = $_POST['shop_email'];

    // Update user profile
    $update_user_query = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, birthday = ? WHERE user_id = ?";
    $update_user_stmt = mysqli_prepare($con, $update_user_query);
    mysqli_stmt_bind_param($update_user_stmt, "sssssi", $first_name, $last_name, $username, $email, $birthday, $user_id);
    $user_update_success = mysqli_stmt_execute($update_user_stmt);

    if ($is_new_merchant) {
        // Create new shop
        $create_shop_query = "INSERT INTO shops (shop_name, owner_id, address, phone_number, email) VALUES (?, ?, ?, ?, ?)";
        $create_shop_stmt = mysqli_prepare($con, $create_shop_query);
        mysqli_stmt_bind_param($create_shop_stmt, "sisss", $shop_name, $user_id, $shop_address, $shop_phone, $shop_email);
        $shop_update_success = mysqli_stmt_execute($create_shop_stmt);
    } else {
        // Update existing shop
        $update_shop_query = "UPDATE shops SET shop_name = ?, address = ?, phone_number = ?, email = ? WHERE owner_id = ?";
        $update_shop_stmt = mysqli_prepare($con, $update_shop_query);
        mysqli_stmt_bind_param($update_shop_stmt, "ssssi", $shop_name, $shop_address, $shop_phone, $shop_email, $user_id);
        $shop_update_success = mysqli_stmt_execute($update_shop_stmt);
    }

    // Set a flag for successful update
    $update_success = $user_update_success && $shop_update_success;

    // Refresh page to show updated information
    header('Location: merchant-settings.php?update_success=' . ($update_success ? '1' : '0'));
    exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            padding: 12px 24px;
            border-radius: 6px;
            transition: all 0.3s;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
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

        .profile-header {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            margin-bottom: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cover-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            padding: 15px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-text {
            margin-left: 20px;
        }

        .shop-name {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-color);
            margin: 0 0 5px 0;
        }

        .owner-name {
            font-size: 16px;
            color: #666;
            margin: 0;
        }
        .shop-email{
            font-size: 12px;
            color: #666;
        }

        .image-upload {
            display: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: inline-block;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
</head>

<body>
    <button id="sidebarToggle" class="btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar flex flex-col h-full">
        <div class="flex-grow">
            <a href="merchant-home.php" class="text-xl font-bold px-4 py-6">
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
        <div class="profile-header" id="coverImageContainer">
            <img src="<?php echo htmlspecialchars($user_profile['cover_image'] ?? '../path/default-cover.jpg'); ?>" alt="Cover Image" class="cover-image" id="coverImage">
            <div class="profile-info">
                <img src="<?php echo htmlspecialchars($user_profile['profile_picture'] ?? $user_profile['shop_logo'] ?? '../path/default-profile.png'); ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                <div class="profile-text">
                    <h1 class="shop-name"><?php echo htmlspecialchars($shop_profile['shop_name'] ?? 'My Shop'); ?></h1>
                    <p class="owner-name"><?php echo htmlspecialchars($user_profile['first_name'] . ' ' . $user_profile['last_name']); ?></p>
                    <p class="shop-email text-gray-350"><?php echo htmlspecialchars($shop_profile['email'] ?? 'No email set'); ?></p>
                </div>
            </div>
        </div>
        <input type="file" id="coverImageUpload" class="image-upload" accept="image/*">
        <input type="file" id="profilePictureUpload" class="image-upload" accept="image/*">

        <h1 class="text-3xl font-bold mb-6">
            <?php echo $is_new_merchant ? 'Welcome! Set Up Your Shop' : 'Merchant Settings'; ?>
        </h1>

        <div class="card mb-6">
            <div class="card-header">
                <?php echo $is_new_merchant ? 'Create Shop Profile' : 'Shop Profile'; ?>
            </div>
            <div class="p-6">
                <form action="" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="first_name" class="block mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_profile['first_name'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="last_name" class="block mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_profile['last_name'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="username" class="block mb-2">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_profile['username'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_profile['email'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="birthday" class="block mb-2">Birthday</label>
                            <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user_profile['birthday'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="shop_name" class="block mb-2">Shop Name</label>
                            <input type="text" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($shop_profile['shop_name'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="shop_address" class="block mb-2">Shop Address</label>
                            <input type="text" id="shop_address" name="shop_address" value="<?php echo htmlspecialchars($shop_profile['address'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="shop_phone" class="block mb-2">Shop Phone Number</label>
                            <input type="text" id="shop_phone" name="shop_phone" value="<?php echo htmlspecialchars($shop_profile['phone_number'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="mb-4">
                            <label for="shop_email" class="block mb-2">Shop Email</label>
                            <input type="email" id="shop_email" name="shop_email" value="<?php echo htmlspecialchars($shop_profile['email'] ?? ''); ?>" class="w-full p-2 border rounded" required>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary mt-6">
                        <?php echo $is_new_merchant ? 'Create Profile' : 'Update Profile'; ?>
                    </button>
                </form>
            </div>
        </div>

        <?php if (!$is_new_merchant): ?>
            
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        $(document).ready(function() {
            $('#coverImageContainer').click(function() {
                $('#coverImageUpload').click();
            });

            $('#profilePicture').click(function(e) {
                e.stopPropagation();
                $('#profilePictureUpload').click();
            });

            function uploadImage(file, imageType) {
                var formData = new FormData();
                formData.append('image', file);
                formData.append('image_type', imageType);

                $.ajax({
                    url: 'merchant-settings.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            if (imageType === 'profile_picture') {
                                $('#profilePicture').attr('src', result.file + '?t=' + new Date().getTime());
                            } else if (imageType === 'cover_image') {
                                $('#coverImage').attr('src', result.file);
                            }
                        } else {
                            alert('Upload failed: ' + result.error);
                        }
                    },
                    error: function() {
                        alert('An error occurred during upload.');
                    }
                });
            }

            $('#coverImageUpload').change(function() {
                uploadImage(this.files[0], 'cover_image');
            });

            $('#profilePictureUpload').change(function() {
                uploadImage(this.files[0], 'profile_picture');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a success message in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const updateSuccess = urlParams.get('update_success');

            if (updateSuccess === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated',
                    text: 'Your profile has been successfully updated!',
                    confirmButtonColor: '#FF7622'
                });
            } else if (updateSuccess === '0') {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'There was an error updating your profile. Please try again.',
                    confirmButtonColor: '#FF7622'
                });
            }

            // Remove the query parameter from the URL
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    </script>
</body>

</html>