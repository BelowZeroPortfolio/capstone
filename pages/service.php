<?php
include_once("../connection/dbcon.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress - Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style_service.css">
</head>

<body>
    <div class="header">
        <header class="bg-dark text-white">
            <nav class="navbar navbar-expand-lg navbar-dark container">
                <a class="navbar-brand" href="../index.php">
                    <img src="../css/logo.png" alt="Logo" class="logo" style="width: 150px;">
                </a>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="service.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row ms-lg-auto mt-2 mt-lg-0">
                        <button class="btn btn-outline-light mb-2 mb-lg-0" id="create_account">Create Account</button>
                        <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Login
                        </button>
                    </div>
                </div>
            </nav>
        </header>
    </div>


    <div class="shop-container">
        <button class="filter-btn d-lg-none d-md-none">Filter</button>
        <div class="sidebar">
            <div class="top-sidebar">
                <h3>Shop</h3>
                <i class="fa-solid fa-xmark"></i>
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
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>
                    <div class="restaurant-item">
                        <div class="restaurant-item-left">
                            <img src="../css/shop-logo1.png" alt="Shop logo 1">
                        </div>
                        <div class="restaurant-item-right">
                            <h4>Jollibee</h4>
                            <p>5 &#11088; </p>
                        </div>
                    </div>

                </div>

                <h3>Products</h3>
                <div class="row row-cols-2 row-cols-xl-4 row-cols-sm-2 g-4">
                    <div class="col">
                        <div class="card pt-3">
                            <div class="d-flex justify-content-center">
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <img src="../css/box1.png" class="card-img-top" alt="...">
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
                                <a href="index.html"><img src="../css/logo.png" class="img-fluid" alt="logo"></a>
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
                                <p>Don’t miss to subscribe to our new feeds, kindly fill the form below.</p>
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
        // const closeButton = document.querySelector('.fa-solid');

        // closeButton.addEventListener('click', function() {
        //     sidebar.style.display = ('none');

        // });
        // Toggle sidebar visibility on button click
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');

            // Change button text based on sidebar state
            if (sidebar.classList.contains('active')) {
                toggleButton.textContent = 'Close';
            } else {
                toggleButton.textContent = 'Filter';
            }
        });

        const closeButton = document.querySelector('.fa-xmark');

        closeButton.addEventListener('click', function() {
            sidebar.classList.remove('active');
            toggleButton.textContent = 'Filter';
        });
    </script>
</body>

</html>