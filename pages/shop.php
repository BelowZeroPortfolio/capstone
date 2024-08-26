<?php
include_once("../connection/dbcon.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php"); // Redirect to login or home page if not logged in
    exit;
}
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
</head>

<body>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

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
            grid-template-columns: 1fr 3fr;
            padding: 0px 50px;
            /* Allows the content to wrap on smaller screens */
        }

        .sidebar {
            width: 250px;
            padding: 20px;
            background-color: #f9f9f9;
            border-right: 1px solid #ddd;
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

        .main-content {
            padding: 20px;
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-bar input[type="text"]::placeholder {
            font-size: 0.8rem;
        }


        .restaurant-listing,
        .product-listing {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .restaurant-listing {
            margin-bottom: 50px;
        }

        .restaurant-item {
            width: 200px;
            height: 80px;
            border-radius: 2px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease-in-out;
        }

        .restaurant-item:hover {
            transform: scale(1.05);
        }

        .restaurant-item-left {
            display: grid;
            place-items: center;
            background-color: #ececec;
            border-radius: 10px;
            min-width: 100px;

        }

        .restaurant-item-left img {
            width: 70px;
            height: 70px;
        }

        .restaurant-item-right {
            text-align: left;
            padding: 15px;
        }

        .restaurant-item-right h4 {
            font-size: 18px;
            font-weight: 00;

        }

        .restaurant-item-right p {
            font-size: 14px;
        }


        .product-item {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }


        .product-item h4 {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .card img {
            width: 100px;
            height: 100px;
        }

        @media (max-width: 768px) {
            .shop-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                width: 80%;
                max-width: 300px;
                z-index: 1000;
                transition: left 0.3s ease-in-out;
                overflow-y: auto;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                width: 100%;
                padding: 10px;
            }

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
        }

        /* Footer css   */
        ul {
            margin: 0px;
            padding: 0px;
        }

        .footer-section {
            background: #151414;
            position: relative;
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

        .copyright-text p {
            margin: 0;
            font-size: 12px;
            color: #878787;
        }

        .copyright-text p a {
            color: #ff5e14;
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
    </style>
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
        <button class="filter-btn d-md-none">Filter</button>
        <div class="sidebar">
            <div class="top-sidebar">
                <h3>Shop</h3>
                <i class="fa-solid fa-xmark d-md-none"></i>
            </div>
            <div class="top-sidebar">
                <h4>Categories</h4>
                <button class="clear-btn">Clear all</button>
            </div>
            <div class="filter-section">
                <h5>Price</h5>
                <div class="filter-options">
                    <label><input type="checkbox"> ₱0 - ₱50.00</label><br>
                    <label><input type="checkbox"> ₱50.00 - ₱100.00</label><br>
                    <label><input type="checkbox"> ₱100.00 - ₱500.00</label><br>
                    <label><input type="checkbox"> ₱500.00 - ₱1000.00</label><br>
                    <label><input type="checkbox"> ₱1000.00 - Above</label><br>
                </div>
            </div>

            <div class="filter-section">
                <h5>Rating</h5>
                <div class="filter-options">
                    <label><input type="radio" name="rating"> All</label><br>
                    <label><input type="radio" name="rating"> 5 Stars Only</label><br>
                    <label><input type="radio" name="rating"> 4 Stars & Above</label><br>
                    <label><input type="radio" name="rating"> 3 Stars & Above</label><br>
                    <label><input type="radio" name="rating"> 2 Stars & Above</label><br>
                    <label><input type="radio" name="rating"> 1 Star & Below</label><br>
                </div>
            </div>

            <div class="filter-section">
                <h5>Filter Three</h5>
                <div class="filter-options">
                    <button class="btn-filter">All</button>
                    <button class="btn-filter">Nearest</button>
                    <button class="btn-filter">Farthest</button>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="search-bar">
                <input type="text" placeholder="Search restaurants or products...">
            </div>

            <div class="listing-section">
                <h3>Restaurants</h3>
                <div class="restaurant-listing">
                    <!-- Repeat this block for each restaurant -->
                    <a href="jollibee.php" class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../image/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </a>
                    <a href="jollibee.php" class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../image/shop-logo1.png" alt="Shop logo 2">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>McDonalds</h4>
                            <p>4.5 &#11088; </p>
                        </div>
                    </a>
                    <a href="jollibee.php" class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../image/shop-logo1.png" alt="Shop logo 2">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>McDonalds</h4>
                            <p>4.5 &#11088; </p>
                        </div>
                    </a>
                    
                </div>

                <h3>Products</h3>
                <div class="row row-cols-2 row-cols-xl-4 row-cols-sm-2 g-4">
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image/box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../image /box1.png" class="card-img-top" alt="...">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">item 1</h5>
                                <div class="d-flex justify-content-between">
                                    <p class="card-text">₱100.00</p>
                                    <span>5 &#11088;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Adjust layout on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>

</html>