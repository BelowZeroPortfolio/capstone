<?php
include_once("../connection/dbcon.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Function to reset filters
function resetFilters()
{
    return [
        'price_range' => 'all',
        'rating' => 'all',
        'sort' => 'default'
    ];
}

// Check if the clear button was pressed
if (isset($_GET['clear'])) {
    $defaults = resetFilters();
    $price_range = $defaults['price_range'];
    $rating = $defaults['rating'];
    $sort = $defaults['sort'];
} else {
    // Initialize filter variables with GET parameters or default values
    $price_range = isset($_GET['price_range']) ? $_GET['price_range'] : 'all';
    $rating = isset($_GET['rating']) ? $_GET['rating'] : 'all';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
}

// Function to get filtered results
function getFilteredResults($con, $price_range, $rating, $sort)
{
    $query = "SELECT s.shop_id, s.shop_name, s.rating, s.logo, u.profile_picture,
                     p.product_id, p.product_name, p.price, p.image_path
              FROM shops s
              JOIN users u ON s.owner_id = u.user_id
              LEFT JOIN products p ON s.shop_id = p.shop_id
              WHERE 1=1";

    if ($price_range != 'all') {
        list($min, $max) = explode('-', $price_range);
        $query .= " AND p.price BETWEEN $min AND $max";
    }

    if ($rating != 'all') {
        $query .= " AND s.rating >= $rating";
    }

    switch ($sort) {
        case 'price_asc':
            $query .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $query .= " ORDER BY p.price DESC";
            break;
        case 'rating_desc':
            $query .= " ORDER BY s.rating DESC";
            break;
        default:
            $query .= " ORDER BY s.rating DESC"; // Default sorting
    }

    $result = mysqli_query($con, $query);
    return $result;
}

// Get filtered results
$filtered_results = getFilteredResults($con, $price_range, $rating, $sort);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress - Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style_sho.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
        }

        .collapse li a {
            color: #ffffff;
            font-size: 1rem;
        }

        .header {
            width: 100%;
            margin: 0;
            position: fixed;
            z-index: 1000;
        }

        .profile-btn {
            border: none;
        }

        .nav-item.active {
            background-color: #ff9548;
            color: #ffffff;
        }

        .container-fluid {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        /* Side bar*/

        .shop-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }

        .main-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sidebar {
            padding: 20px;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
        }

        .product-content {
            padding: 20px;
        }

        .checkout-sidebar {
            margin-top: 50px;
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 300px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            z-index: 10;
        }

        .checkout-sidebar h3 {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item p {
            margin: 0;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin: 0 5px;
        }

        .quantity-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .cart-total {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .checkout-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .checkout-btn:hover {
            background-color: #0056b3;
        }

        .top-sidebar {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            /* Ensures proper alignment */
        }

        .top-sidebar button {
            font-size: 0.8rem;
            padding: 0px 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            white-space: nowrap;
            /* Prevents button text from wrapping */
            width: auto;
            /* Ensure button width is not stretching */
        }

        .top-sidebar i {
            width: 15px;
            height: 15px;
        }

        .top-sidebar i:hover {
            border: 1px solid #f7af8d;
            /* box-shadow: rgba(252, 220, 156, 0.733) 0px 3px 8px; */
            cursor: pointer;
        }

        .top-sidebar h4 {
            position: relative;
            top: 5px;
            font-size: 20px;
        }

        .filter-section {
            border-top: 1px solid #000000;
            margin: 20px 0px;
        }

        .filter-section h5 {
            margin: 10px 0px;
            font-size: 1rem;
        }

        .filter-options label {
            font-size: 0.9rem;
        }

        .filter-options input[type="checkbox"],
        .filter-options input[type="radio"] {
            height: 20px;
            width: 20px;
            vertical-align: middle;
        }

        .filter-options input[type="checkbox"],
        .filter-options input[type="radio"]:hover {
            outline: #ff5e14;
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .search-bar input[type="text"] {
            width: 100%;
            padding: 12px 20px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .search-bar input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .search-bar input[type="text"]::placeholder {
            font-size: 0.8rem;
        }

        .listing-section h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .restaurant-listing,
        .product-listing {
            display: grid;
            gap: 20px;
        }

        .restaurant-listing {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .product-listing {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 10px;
        }

        .restaurant-item,
        .product-item {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }


        .restaurant-item:hover,
        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .restaurant-item {
            display: flex;
            flex-direction: column;
            height: 200px;
        }

        .restaurant-item-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .restaurant-item-left img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
        }

        .restaurant-item-right {
            padding: 15px;
            text-align: center;
        }

        .product-item {
            flex: 0 0 auto;
            width: 150px;
            /* Adjust the width as needed */
            height: 200px;
            /* Adjust the height as needed */
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-item img {
            width: 100%;
            height: 100px;
            /* Adjust the height as needed */
            object-fit: cover;
        }

        .product-item-info {
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-item h4,
        .restaurant-item h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #333;
        }

        .product-item p,
        .restaurant-item p {
            font-size: 0.8rem;
            color: #666;
            margin: 2px 0;
        }

        @media (max-width: 1200px) {
            .shop-container {
                grid-template-columns: 1fr;
            }

            .main-content {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.active {
                left: 0;
            }

            .checkout-sidebar {
                position: fixed;
                right: -300px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: right 0.3s ease-in-out;
            }

            .checkout-sidebar.active {
                right: 0;
            }
        }

        @media (max-width: 768px) {

            .restaurant-listing,
            .product-listing {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .restaurant-item {
                width: 100%;
            }

            .search-bar {
                flex-direction: column;
            }

            .search-bar input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .product-listing {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .product-item img {
                width: 80px;
                height: 80px;
            }

            .product-item h4 {
                font-size: 0.8rem;
            }

            .product-item p {
                font-size: 0.7rem;
            }
        }

        /* Footer css   */
        ul {
            margin: 0px;
            padding: 0px;
        }

        .footer-section {
            background: #151414;
            position: relative;
            z-index: 11;
        }

        .footer-cta {
            border-bottom: 1px solid #373636;
        }

        .single-cta i {
            color: #ff5e14;
            font-size: 20px;
            float: left;
            margin-top: 8px;
        }

        .cta-text {
            padding-left: 15px;
            display: inline-block;
        }

        .cta-text h4 {
            color: #fff;
            font-size: 12px;
            font-weight: 400;
            margin-bottom: 2px;
        }

        .cta-text span {
            color: #757575;
            font-size: 15px;
        }

        .footer-content {
            position: relative;
            z-index: 2;
        }

        .footer-pattern img {
            position: absolute;
            top: 0;
            left: 0;
            height: 330px;
            background-size: cover;
            background-position: 100% 100%;
        }

        .footer-logo {
            margin-bottom: 30px;
        }

        .footer-logo img {
            max-width: 200px;
        }

        .footer-text p {
            margin-bottom: 14px;
            font-size: 14px;
            color: #7e7e7e;
            line-height: 28px;
        }

        .footer-social-icon span {
            color: #fff;
            display: block;
            font-size: 20px;
            font-weight: 700;
            font-family: "Poppins", sans-serif;
            margin-bottom: 20px;
        }

        .footer-social-icon a {
            color: #fff;
            font-size: 16px;
            margin-right: 15px;
        }

        .footer-social-icon i {
            height: 40px;
            width: 40px;
            text-align: center;
            line-height: 38px;
            border-radius: 50%;
        }

        .facebook-bg {
            background: #3b5998;
        }

        .twitter-bg {
            background: #55acee;
        }

        .google-bg {
            background: #dd4b39;
        }

        .footer-widget-heading h3 {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 40px;
            position: relative;
        }

        .footer-widget-heading h3::before {
            content: "";
            position: absolute;
            left: 0;
            bottom: -15px;
            height: 2px;
            width: 50px;
            background: #ff5e14;
        }

        .footer-widget ul li {
            float: left;
            width: 50%;
            margin-bottom: 12px;
        }

        .footer-widget ul li a:hover {
            color: #ff5e14;
        }

        .footer-widget ul li a {
            font-size: 14px;
            color: #878787;
            text-transform: capitalize;
        }

        .subscribe-form {
            position: relative;
            overflow: hidden;
        }

        .subscribe-form input {
            width: 100%;
            padding: 14px 28px;
            background: #2e2e2e;
            border: 1px solid #2e2e2e;
            color: #fff;
        }

        .subscribe-form button {
            position: absolute;
            right: 0;
            background: #ff5e14;
            padding: 13px 20px;
            border: 1px solid #ff5e14;
            top: 0;
        }

        .subscribe-form button i {
            color: #fff;
            font-size: 22px;
            transform: rotate(-6deg);
        }

        .copyright-area {
            background: #202020;
            padding: 25px 0;
        }

        .copyright-text {
            p {
                margin: 0;
                font-size: 12px;
                color: #878787;
            }

            p a {
                color: #ff5e14;
            }
        }

        .footer-menu li {
            display: inline-block;
            margin-left: 20px;
        }

        .footer-menu li:hover a {
            color: #ff5e14;
        }

        .footer-menu li a {
            font-size: 12px;
            color: #878787;
        }

        .clear-btn {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #495057;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .clear-btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .apply-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 15px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .apply-btn:hover {
            background-color: #0056b3;
        }

        .restaurant-listing {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 10px;
        }

        .restaurant-item {
            flex: 0 0 auto;
            width: 150px;
            /* Adjust the width as needed */
            height: 200px;
            /* Adjust the height as needed */
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .restaurant-item-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding: 10px;
            /* Adjust the padding as needed */
        }

        .restaurant-item-left img {
            max-width: 100%;
            max-height: 80px;
            /* Adjust the height as needed */
            object-fit: contain;
        }

        .restaurant-item-right {
            padding: 10px;
            text-align: center;
        }

        .restaurant-item h4 {
            font-size: 0.9rem;
            /* Adjust the font size as needed */
            margin-bottom: 5px;
            color: #333;
        }

        .restaurant-item p {
            font-size: 0.8rem;
            /* Adjust the font size as needed */
            color: #666;
            margin: 2px 0;
        }

        .restaurant-listing-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .scroll-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s;
            z-index: 10;
        }

        .scroll-button:hover {
            background-color: #0056b3;
        }

        .scroll-button.prev {
            left: 10px;
        }

        .scroll-button.next {
            right: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <header class="bg-dark text-white">
            <nav class="navbar navbar-expand-lg navbar-dark container">
                <a class="navbar-brand" href="home.php">
                    <img src="../image/logo.png" alt="Logo" class="logo" style="width: 150px;">
                </a>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                        <li class="nav-item active" aria-current="page"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="service.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row ms-lg-auto mt-2 mt-lg-0 gap-3">
                        <a href="cart.php" class="btn btn-outline-light">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                        <button class="profile-btn bg-dark text-white" id="profileIcon" onclick="window.location.href='profile.php';">
                            <i class='bx bx-user'></i>
                        </button>
                        <button class="btn btn-outline-light" id="logoutButton" onclick="window.location.href='logout.php';">
                            Logout
                        </button>
                    </div>
                </div>
            </nav>
        </header>
    </div>

    <div class="shop-container">
        <div class="main-content">
            <button class="filter-btn d-md-none">Filter</button>
            <div class="sidebar">
                <div class="top-sidebar">
                    <h3>Shop</h3>
                    <i class="fa-solid fa-xmark d-md-none"></i>
                </div>
                <form action="shop.php" method="GET">
                    <div class="top-sidebar">
                        <h4>Categories</h4>
                        <button type="submit" name="clear" value="1" class="clear-btn">Clear all</button>
                    </div>
                    <div class="filter-section">
                        <h5>Price</h5>
                        <div class="filter-options">
                            <label><input type="radio" name="price_range" value="all" <?php echo $price_range == 'all' ? 'checked' : ''; ?>> All</label><br>
                            <label><input type="radio" name="price_range" value="0-50" <?php echo $price_range == '0-50' ? 'checked' : ''; ?>> ₱0 - ₱50.00</label><br>
                            <label><input type="radio" name="price_range" value="50-100" <?php echo $price_range == '50-100' ? 'checked' : ''; ?>> ₱50.00 - ₱100.00</label><br>
                            <label><input type="radio" name="price_range" value="100-500" <?php echo $price_range == '100-500' ? 'checked' : ''; ?>> ₱100.00 - ₱500.00</label><br>
                            <label><input type="radio" name="price_range" value="500-1000" <?php echo $price_range == '500-1000' ? 'checked' : ''; ?>> ₱500.00 - ₱1000.00</label><br>
                            <label><input type="radio" name="price_range" value="1000-999999" <?php echo $price_range == '1000-999999' ? 'checked' : ''; ?>> ₱1000.00 - Above</label><br>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h5>Rating</h5>
                        <div class="filter-options">
                            <label><input type="radio" name="rating" value="all" <?php echo $rating == 'all' ? 'checked' : ''; ?>> All</label><br>
                            <label><input type="radio" name="rating" value="5" <?php echo $rating == '5' ? 'checked' : ''; ?>> 5 Stars Only</label><br>
                            <label><input type="radio" name="rating" value="4" <?php echo $rating == '4' ? 'checked' : ''; ?>> 4 Stars & Above</label><br>
                            <label><input type="radio" name="rating" value="3" <?php echo $rating == '3' ? 'checked' : ''; ?>> 3 Stars & Above</label><br>
                            <label><input type="radio" name="rating" value="2" <?php echo $rating == '2' ? 'checked' : ''; ?>> 2 Stars & Above</label><br>
                            <label><input type="radio" name="rating" value="1" <?php echo $rating == '1' ? 'checked' : ''; ?>> 1 Star & Above</label><br>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h5>Sort By</h5>
                        <div class="filter-options">
                            <select name="sort">
                                <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Default</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating_desc" <?php echo $sort == 'rating_desc' ? 'selected' : ''; ?>>Highest Rated</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="apply-btn">Apply Filters</button>
                </form>
            </div>

            <div class="product-content">
                <div class="search-bar">
                    <input type="text" placeholder="Search restaurants or products...">
                </div>

                <div class="listing-section">
                    <h3>Shops</h3>
                    <div class="restaurant-listing-container">
                        <button class="scroll-button prev" onclick="scrollLeft()">&#10094;</button>
                        <div class="restaurant-listing">
                            <?php
                            $seen_shops = array();
                            mysqli_data_seek($filtered_results, 0);
                            while ($row = mysqli_fetch_assoc($filtered_results)) {
                                if (!in_array($row['shop_id'], $seen_shops)) {
                                    $seen_shops[] = $row['shop_id'];
                                    $shop_id = $row['shop_id'];
                                    $shop_name = htmlspecialchars($row['shop_name']);
                                    $rating = number_format($row['rating'], 1);
                                    $logo = !empty($row['logo']) ? htmlspecialchars($row['logo']) : (!empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : '../path/default_shop_logo.png');

                                    echo '<a href="shop_details.php?id=' . $shop_id . '" class="restaurant-item">';
                                    echo '<div class="restaurant-item-left">';
                                    echo '<img src="../product_images/' . $logo . '" alt="' . $shop_name . ' logo">';
                                    echo '</div>';
                                    echo '<div class="restaurant-item-right">';
                                    echo '<h4>' . $shop_name . '</h4>';
                                    echo '<p>' . $rating . ' &#11088;</p>';
                                    echo '</div>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                        <button class="scroll-button next" onclick="scrollRight()">&#10095;</button>
                    </div>

                    <h3>Products</h3>
                    <div class="product-listing">
                        <?php
                        mysqli_data_seek($filtered_results, 0);
                        while ($row = mysqli_fetch_assoc($filtered_results)) {
                            if (isset($row['product_id'])) {
                                $product_id = $row['product_id'];
                                $product_name = htmlspecialchars($row['product_name']);
                                $price = number_format($row['price'], 2);
                                $image_path = !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : '../image/default_product_image.png';
                                $shop_name = htmlspecialchars($row['shop_name']);

                                echo '<div class="product-item">';
                                echo '<img src="' . $image_path . '" alt="' . $product_name . '">';
                                echo '<button class="add-to-cart-btn" data-product-id="' . $product_id . '" data-product-name="' . $product_name . '" data-product-price="' . $price . '">+</button>';
                                echo '<div class="product-item-info">';
                                echo '<h4>' . $product_name . '</h4>';
                                echo '<p>₱' . $price . '</p>';
                                echo '<p>' . $row['rating'] . ' &#11088;</p>';
                                echo '<p>Sold by: ' . $shop_name . '</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="checkout-sidebar">
            <h3>Your Cart</h3>
            <div id="cart-items">
                <!-- Cart items will be dynamically added here -->
            </div>
            <div class="cart-total">
                <p>Total: ₱<span id="cart-total">0.00</span></p>
            </div>
            <button class="checkout-btn">Proceed to Checkout</button>
        </div>
    </div>

    <hr>

    <footer class="footer-section">
        <div class="container">
            <div class="footer-cta pt-5 pb-5">
                <div class="row">
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="cta-text">
                                <h4>Find us</h4>
                                <span>101 Avenue, Brgy. Zone 6, Pulupandan</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="fas fa-phone"></i>
                            <div class="cta-text">
                                <h4>Call us</h4>
                                <span>09123456789</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 mb-30">
                        <div class="single-cta">
                            <i class="far fa-envelope-open"></i>
                            <div class="cta-text">
                                <h4>Mail us</h4>
                                <span>bagoexpress@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-content pt-5 pb-5">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 mb-50">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="index.html"><img src="../image/logo.png" class="img-fluid" alt="logo"></a>
                            </div>
                            <div class="footer-text">
                                <p>Connecting Community,<br>
                                    Delivering Delights: Your Modernized <br>
                                    Transport and Delivery Services</p>
                            </div>
                            <div class="footer-social-icon">
                                <span>Follow us</span>
                                <a href="#"><i class="fab fa-facebook-f facebook-bg"></i></a>
                                <a href="#"><i class="fab fa-twitter twitter-bg"></i></a>
                                <a href="#"><i class="fab fa-google-plus-g google-bg"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Useful Links</h3>
                            </div>
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#">about</a></li>
                                <li><a href="#">services</a></li>
                                <li><a href="#">portfolio</a></li>
                                <li><a href="#">Contact</a></li>
                                <li><a href="#">About us</a></li>
                                <li><a href="#">Our Services</a></li>
                                <li><a href="#">Expert Team</a></li>
                                <li><a href="#">Contact us</a></li>
                                <li><a href="#">Latest News</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 mb-50">
                        <div class="footer-widget">
                            <div class="footer-widget-heading">
                                <h3>Subscribe</h3>
                            </div>
                            <div class="footer-text mb-25">
                                <p>Don't miss to subscribe to our new feeds, kindly fill the form below.</p>
                            </div>
                            <div class="subscribe-form">
                                <form action="#">
                                    <input type="text" placeholder="Email Address">
                                    <button><i class="fab fa-telegram-plane"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg text-center justify-content-center">
                        <div class="copyright-text">
                            <p>Copyright &copy; 2024, All Right Reserved</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </footer>

    <script>
        // Get the sidebar and toggle button elements
        const sidebar = document.querySelector('.sidebar');
        const toggleButton = document.querySelector('.filter-btn');
        const closeButton = document.querySelector('.fa-xmark');

        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });

        closeButton.addEventListener('click', function() {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Function to add product to cart
        function addToCart(productId, productName, productPrice) {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            let existingItem = cartItems.querySelector(`[data-product-id="${productId}"]`);

            if (existingItem) {
                // Update quantity if item already exists
                const quantitySpan = existingItem.querySelector('.quantity');
                let quantity = parseInt(quantitySpan.textContent);
                quantity += 1;
                quantitySpan.textContent = quantity;

                // Update total
                const currentTotal = parseFloat(cartTotal.textContent);
                const newTotal = currentTotal + parseFloat(productPrice);
                cartTotal.textContent = newTotal.toFixed(2);
            } else {
                // Add new item to cart
                const newItem = document.createElement('div');
                newItem.classList.add('cart-item');
                newItem.setAttribute('data-product-id', productId);
                newItem.innerHTML = `
                <p>${productName} - ₱${productPrice}</p>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="updateQuantity(this, -1, ${productPrice})">-</button>
                    <span class="quantity">1</span>
                    <button class="quantity-btn" onclick="updateQuantity(this, 1, ${productPrice})">+</button>
                </div>
                <button class="delete-btn" onclick="deleteItem(this, ${productPrice})">Delete</button>
            `;
                cartItems.appendChild(newItem);

                // Update total
                const currentTotal = parseFloat(cartTotal.textContent);
                const newTotal = currentTotal + parseFloat(productPrice);
                cartTotal.textContent = newTotal.toFixed(2);
            }
        }

        // Function to update quantity
        function updateQuantity(button, change, productPrice) {
            const quantitySpan = button.parentElement.querySelector('.quantity');
            let quantity = parseInt(quantitySpan.textContent);
            quantity += change;
            if (quantity < 1) quantity = 1;
            quantitySpan.textContent = quantity;

            // Update total
            const cartTotal = document.getElementById('cart-total');
            const currentTotal = parseFloat(cartTotal.textContent);
            const newTotal = currentTotal + (change * productPrice);
            cartTotal.textContent = newTotal.toFixed(2);
        }

        // Function to delete item
        function deleteItem(button, productPrice) {
            const cartItem = button.parentElement;
            const quantity = parseInt(cartItem.querySelector('.quantity').textContent);
            const cartTotal = document.getElementById('cart-total');
            const currentTotal = parseFloat(cartTotal.textContent);
            const newTotal = currentTotal - (quantity * productPrice);
            cartTotal.textContent = newTotal.toFixed(2);
            cartItem.remove();
        }

        // Add event listeners to all add-to-cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const productPrice = this.getAttribute('data-product-price');
                addToCart(productId, productName, productPrice);
            });
        });

        // Function to scroll left
        function scrollLeft() {
            const container = document.querySelector('.restaurant-listing');
            container.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        }

        // Function to scroll right
        function scrollRight() {
            const container = document.querySelector('.restaurant-listing');
            container.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        }

        // Function to handle checkout
        function proceedToCheckout() {
            const cartItems = document.querySelectorAll('.cart-item');
            const orders = [];

            cartItems.forEach(item => {
                const productId = item.getAttribute('data-product-id');
                const productName = item.querySelector('p').textContent.split(' - ')[0];
                const productPrice = parseFloat(item.querySelector('p').textContent.split('₱')[1]);
                const quantity = parseInt(item.querySelector('.quantity').textContent);

                orders.push({
                    productId,
                    productName,
                    productPrice,
                    quantity
                });
            });

            // Redirect to checkout.php with orders data
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'checkout.php';

            const ordersInput = document.createElement('input');
            ordersInput.type = 'hidden';
            ordersInput.name = 'orders';
            ordersInput.value = JSON.stringify(orders);

            form.appendChild(ordersInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Add event listener to the checkout button
        document.querySelector('.checkout-btn').addEventListener('click', proceedToCheckout);

        // Function to scroll left
        function scrollLeft() {
            const container = document.querySelector('.restaurant-listing');
            container.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        }

        // Function to scroll right
        function scrollRight() {
            const container = document.querySelector('.restaurant-listing');
            container.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>