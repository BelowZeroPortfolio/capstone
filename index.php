<?php
include_once("connection/dbcon.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="header">
        <header class="bg-dark text-white">
            <nav class="navbar navbar-expand-lg navbar-dark container">
                <a class="navbar-brand" href="index.php">
                    <img src="css/logo.png" alt="Logo" class="logo" style="width: 150px;">
                </a>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/service.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/contact.php">Contact</a></li>
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



    <!-- Modal ni sang login-->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="post" class="form-control">
                        <label for="username">Username:</label><br>
                        <input type="text" id="username" name="username" placeholder="Enter username"><br>
                        <label for="password">Password:</label><br>
                        <input type="password" id="password" name="password" placeholder="Enter password"><br>
                        <input type="submit" name="submit" value="submit"><br>
                        <span>Don't have an account? <a href="#">Signup</a></span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="containers">
        <div class="content">
            <div class="col-left">
                <h2>Connecting Community,<br>
                    Delivering Delights:<br>
                    Your Modernized Transport and Delivery Services</h2>
                <span>Experience reliable, convenient transport and delivery with us. Join thousands of satisfied customers today.</span>
                <div class="input-group">
                    <div class="input-container">
                        <input type="text" class="form-controls" placeholder="Enter location">
                        <span class="locate-me"><i class='bx bx-target-lock'></i>Locate me</span>
                    </div>
                    <button class="btn-index" type="button">Search</button>
                </div>
            </div>
            <div class="col-right">
                <img src="index-bg.png" alt="Background Image">
            </div>
        </div>
    </div>

    <h6 style="text-align: left; margin-left: 50px; padding-top: 20px;">Travel Destination</h6>
    <div class="browse-btn">
        <a href="browse-more.php" style="text-align: right;">Browse More >></a>

    </div>
    <div class="scroll-category">
        <div class="img-box">
            <a href="mymtccc.php">
                <img src="css/mymtccc.jpg" alt="image1">
                <div class="img-header">
                    <p>MYMTCCC</p>
                </div>
            </a>

        </div>
        <div class="img-box">
            <img src="css/javellana-mansion.jpg" alt="image1">
            <div class="img-header">
                <p>Javellana Mansion</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/kipot.jpg" alt="image1">
            <div class="img-header">
                <p>Kipot Twin Falls</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/bantayan.jpg" alt="image1">
            <div class="img-header">
                <p>Bantayan Park</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/buenos.jpg" alt="image1">
            <div class="img-header">
                <p>Buenos Aires</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/giannas.jpg" alt="image1">
            <div class="img-header">
                <p>Gianna's Mt. Resort</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/rafael.jpg" alt="image1">
            <div class="img-header">
                <p>Rafael Salas Park</p>
            </div>
        </div>
        <div class="img-box">
            <img src="css/j.jpg" alt="image1">
            <div class="img-header">
                <p>J Resort</p>
            </div>
        </div>
    </div>


    <div class="back">
        <h6 style="text-align: center; padding: 25px;">What services we offer</h6>
        <div class="content-container">
            <div class="content-box">
                <div class="box">
                    <div class="icon">
                        <i class='bx bxs-shopping-bags'></i>
                    </div>
                    <h5>Shop Online</h5>
                    <p>Browse and buy from a wide range of products with ease. Enjoy convenient online shopping and have your items delivered directly to your door.</p>
                </div>
                <div class="box">
                    <div class="icon">
                        <i class='bx bxs-car'></i>
                    </div>
                    <h5>Ride Hailing</h5>
                    <p>Get a ride with just a few taps. Our ride-hailing service connects you with reliable drivers, ensuring a comfortable and timely journey.</p>
                </div>
                <div class="box">
                    <div class="icon">
                        <i class='bx bx-package'></i>
                    </div>
                    <h5>Pabili</h5>
                    <p>Need something picked up or delivered? Use our 'Pabili' service to handle errands and deliveries with simplicity and speed</p>
                </div>
            </div>
        </div>

        <div class="explore-btn">
            <div class="btn">
                <button>Explore here</button>
            </div>
        </div>


        <hr>

    </div>
    <div class="feature-container">
        <div class="feature">
            <div class="col-lefts">
                <img src="css/box1.png" class="image-box" alt="Feature1-Image">
            </div>
            <div class="col-rights">
                <h2>Shop online</h2>
                <p>Discover a world of convenience with BagoExpress's online shopping service. Easily browse, select, and have your favorite products delivered right to your doorstep, saving you time and effort.</p>
                <button class="feature-btn">Learn more</button>
            </div>

        </div>
        <br>
        <div class="feature">
            <div class="col-leftss">
                <h2>Ride Hailing</h2>
                <p>Get where you need to go quickly and safely with our reliable ride-hailing service. Whether it's a daily commute or a special trip, BagoExpress ensures a comfortable journey every time.</p>
                <button class="feature-btn">Learn more</button>
            </div>
            <div class="col-rightss">
                <img src="css/box2.png" class="image-box" alt="Feature2-Image">
            </div>
        </div>
        <br>
        <div class="feature">  
            <div class="col-lefts">
                <img src="css/box3.png" class="image-box" alt="Feature3-Image">
            </div>
            <div class="col-rights">
                <h2>Pabili</h2>
                <p>Need something picked up? Our Pabili service is here to help. From groceries to essentials, we’ll run the errand for you, so you can focus on what matters most.</p>
                <button class="feature-btn">Learn more</button>
            </div>
        </div>
    </div>



    <br><br>

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
                                <a href="index.html"><img src="css/logo.png" class="img-fluid" alt="logo"></a>
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


    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>