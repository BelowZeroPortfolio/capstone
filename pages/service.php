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
    <title>BagoExpress - Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
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

    .profile-btn {
        border: none;
    }

    .nav-item.active {
        background-color: #ff9548;
        color: #ffffff;
    }

    .header {
        width: 100%;
        margin: 0;
    }

    .container-fluid {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .containers {
        background-color: #ff9548;
        height: 100vh;
        margin-bottom: 50px;
    }

    .content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        max-width: 100%;
        width: 100%;
        padding: 50px 20px;
    }

    .col-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        margin-left: 30px;
    }

    .col-left h2 {
        color: #ffffff;
        font-weight: 700;
        margin-bottom: 20px;

    }

    .col-left span {
        color: #ffffff;
        margin-bottom: 30px;
    }

    .buttons-index {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #000010;
        padding: 0px 10px;
    }

    .buttons-index button {
        padding: 10px 20px;
        border: none;
        background-color: #d7d4d2;
        color: white;
        cursor: pointer;
        transition: 0.2s ease-in-out;
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    }

    .buttons-index button:hover {
        background-color: #ff9548;
        color: white;
        transform: scale(1.05);
    }

    .buttons-index input[type=text] {
        outline: none;
        border: none;
    }

    .buttons-index input[type=text]::placeholder {
        font-size: 12px;
    }



    .input-group {
        display: flex;
        align-items: center;
        background-color: #ffffff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    }

    .input-container {
        position: relative;
        flex-grow: 1;
    }

    .input-container span {
        color: #ff9548;
    }

    .form-controls {
        width: 90%;
        padding-right: 70px;
        /* Adjust based on 'Locate me' width */
        border: none;
    }

    .locate-me {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #ff9548;
        /* Customize the color */
    }

    .locate-me i {
        font-size: 20px;
        position: relative;
        top: 3px;
        right: 2px;
    }

    .input-group .btn-index {
        padding: 5px 10px;
        color: #ffffff;
        background-color: #ff9548;
        margin-left: 20px;
        border: none;
        border-radius: 10px;
    }





    .col-right {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .col-right img {
        max-width: 100%;
        padding-right: 15px;
    }

    .content-container {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .back {
        background-color: #e2e2e2;
    }

    .content-box {
        display: grid;
        place-items: center;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px 30px;
        margin: 0px auto;
    }

    .box {
        width: 100%;
        height: 200px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .box:hover {
        transform: scale(1.05);
        transition: ease-in-out 0.5s;
    }

    .box p {
        margin-top: 15px;
        font-size: 0.7rem;
    }

    .icon i {
        transform: scale(2);
        padding: 20px 0px;
        color: #ff9548;
    }

    h3 {
        margin-bottom: 10px;
        font-size: 1.5em;
        color: #333;
    }

    p {
        font-size: 1em;
        color: #666;
    }

    .browse-btn {
        width: 100%;
        text-align: end;
        padding: 0px 50px;
    }

    .browse-btn a {
        color: #ffffff;
        background-color: #ff9548;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 10px;
        text-align: end;
    }

    .explore-btn {
        display: grid;
        place-items: center;
        margin: 20px auto;
    }

    .explore-btn button {
        padding: 15px 25px;
        background-color: #ff9548;
        border-radius: 10px;
        border: none;
        color: #f1efed;
    }

    .scroll-category {
        display: grid;
        grid-template-columns: repeat(auto-fill,
                minmax(250px, 1fr));
        /* Adjusts the number of columns based on available space */
        gap: 20px;
        padding: 20px 50px 50px;
    }

    .img-box {
        width: 100%;
        /* Ensure it uses the full width of the grid cell */
        height: 200px;
        background-color: #f9f9f9;
        /* Background color for better contrast */
        text-align: center;
        border-radius: 20px;
    }

    .img-box img {
        width: 100%;
        /* Maintain aspect ratio */
        height: 200px;
        /* Adjust height as needed */
        object-fit: cover;
        border-radius: 20px;
    }

    .img-box a {
        text-decoration: none;
    }

    .img-header {
        width: 180px;
    }

    .img-header p {
        font-size: 14px;
        font-weight: 500;
        padding: 5px 2px;
        background-color: #666;
        border-radius: 8px;
        background-color: #ffffff;
        color: #000010;
        position: relative;
        top: -40px;
        left: 10px;
        z-index: 20;
    }

    /* Feature  Styling*/
    .feature-container {
        width: 100%;
        display: grid;
        place-items: center;
    }

    .feature {
        display: grid;
        grid-template-columns: 1fr 1fr;
        padding: 50px;
    }

    .col-rights {
        margin: auto 0px;
        text-align: left;
    }

    .col-lefts {
        margin: auto;
    }

    .feature-btn {
        color: #ffffff;
        border: none;
        background-color: #ff9548;
        border-radius: 10px;
        padding: 10px 15px;
    }

    .col-leftss {
        margin: auto;
        text-align: left;
    }

    .col-rightss {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .service-container {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    .service-con {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .service-section {
        background: linear-gradient(135deg, #ff7e5f, #feb47b);
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        color: #fff;
    }

    .service-section h2 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #fff;
    }

    .service-section p {
        font-size: 12px;
        margin-bottom: 15px;
        color: #fff;
    }

    .service-section .btn {
        background-color: #ffffff;
        color: #424242;
        font-size: 12px;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .service-section .btn:hover {
        background-color: #d4d4d4;
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

    @media (max-width: 1000px) {
        .about-right {
            transform: scale(0.8);
            transition: ease-in-out 0.2s;
        }
    }

    /* Responsive layout for smaller screens */
    @media (max-width: 768px) {
        .nav-item a {
            text-align: center;
        }

        .content {
            margin-top: 30px;
            grid-template-columns: 1fr;
        }

        .back {
            margin-top: 100px;
        }

        .col-left {
            padding-top: 20px;
        }

        .col-left h2 {
            font-size: 24px;
        }

        .col-left span {
            margin: none;
        }

        .col-right img {
            padding: 30px;
        }

        .content-box {
            grid-template-columns: 1fr;
        }

        .scroll-category {
            grid-template-columns: repeat(2, 1fr);
        }

        .about-con {
            grid-template-columns: 1fr;
            padding: 10px 20px;
        }

        .about-left {
            margin: 0;
        }

        .about-top {
            max-width: 500px;
            height: auto;
        }

        .about-right {
            padding: 50px;
            transform: scale(0.95);
            transition: ease-in-out 0.2s;
        }

        .feature-container {
            width: 100%;
        }

        .feature {
            display: grid;
            grid-template-columns: 1fr;
            padding: 10px;
        }

        .col-leftss {
            order: 2;
        }

        .col-rightss {
            order: 1;
        }

        .feature h2 {
            font-size: 20px;
        }

        .feature p {
            font-size: 14px;
        }

        .feature img {
            width: 100%;
            height: auto;
        }
    }
</style>

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
                        <li class="nav-item active" aria-current="page"><a class="nav-link" href="service.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
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

    <div class="service-container">
        <div class="service-con">
            <div class="row">
                <div class="col-md-4">
                    <section class="service-section">
                        <h2>Ride-Hailing</h2>
                        <p>Book a ride quickly and easily. Enjoy real-time tracking, affordable pricing, and various vehicle options, including tricycles. Available 24/7.</p>
                        <button class="btn btn-white">Book a Ride</button>
                    </section>
                </div>
                <div class="col-md-4">
                    <section class="service-section">
                        <h2>Travel & Transportation</h2>
                        <p>Comfortable and convenient long-distance travel. Professional drivers, advance scheduling for airport transfers, and special trips.</p>
                        <button class="btn btn-primary">Schedule a Ride</button>
                    </section>
                </div>
                <div class="col-md-4">
                    <section class="service-section">
                        <h2>Additional Services</h2>
                        <p>Flexible solutions for package delivery, special ride requests, and group travel. Reliable and accommodating service.</p>
                        <button class="btn btn-primary">Learn More</button>
                    </section>
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