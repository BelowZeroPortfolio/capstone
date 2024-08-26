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
    <title>BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style_about.css">
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
                        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="service.php">Services</a></li>
                        <li class="nav-item  active" aria-current="page"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row ms-lg-auto mt-2 mt-lg-0 gap-3">
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

    <div class="about-container">
        <div class="about-content">
            <h1>Driving progress by delivering seamless and reliable transportation solutions for everyone.</h1><br>
            <p>At BagoExpress, we're not just moving packages—we're connecting communities, one delivery at a time.</p>
        </div>
    </div>
    <div class="container-about-us">
        <div class="content-about-us">
            <div class="col-left">
                <h6>About us</h6>
                <h1>ABOUT [<strong>BagoExpress</strong>]</h1>
                <p>
                    We are committed to transforming the way you experience transportation and delivery services. With a focus on reliability and convenience, we aim to bridge the gap between communities, bringing people closer to the things they need most. Our innovative approach combines modern technology with a deep understanding of local needs, ensuring that every ride, every delivery, and every experience is smooth, efficient, and tailored to your satisfaction. <br><br>
                    Our mission is to provide a seamless transportation service that not only meets but exceeds expectations. Whether you're booking a ride, sending a package, or simply exploring new possibilities, BagoExpress is here to support your journey. We believe in the power of connectivity, and our dedicated team works tirelessly to ensure that our services are always accessible, affordable, and aligned with the ever-changing demands of our customers. Join us as we drive progress, one ride at a time.
                </p>
            </div>
            <div class="col-right">
                <img src="../image/about-top.webp" alt="Image in About us">
            </div>
        </div>
    </div>

    <div class="team-container">
        <h6>Our Team</h6>
        <h1>Developer</h1>
        <p>Meet the passionate and creative minds driving our vision forward, each bringing unique expertise and dedication to our mission.</p>
        <div class="team-section">
            <div class="team-member">
                <img src="../image/team1.png" alt="team1 image">
                <h4>Carl Navid Cata-an</h4>
                <h5>Team Leader</h5>
                <p>drives our team with strategic vision and a commitment to excellence.</p>
            </div>

            <div class="team-member">
                <img src="../image/team2.png" alt="team2 image">
                <h4>Niel Allen Osorio</h4>
                <h5>UI/UX Designer</h5>
                <p>Niel creates intuitive, user-friendly designs that stand out visually.</p>
            </div>

            <div class="team-member">
                <img src="../image/team3.png" alt="team3 image">
                <h4>Darwin Demaclid</h4>
                <h5>Front-End Developer</h5>
                <p>Darwin ensures our designs function flawlessly with clean, responsive code.</p>
            </div>

        </div>
    </div>

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
</body>

</html>