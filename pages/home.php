<?php
include_once("../connection/dbcon.php"); // Include your database connection file

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php"); // Redirect to login or home page if not logged in
    exit;
}

// Close the database connection
$con->close();
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

        .nav-item a:hover {
            color: #ff9548;
        }

        .nav-item.active {
            background-color: #ff9548;
            color: #ffffff;
        }

        .header {
            width: 100%;
            margin: 0;
        }

        .profile-btn {
            border: none;
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
        }

        .buttons-index button:hover {
            background-color: #ff9548;
            color: white;
            transform: scale(1.05);
        }

        .buttons-index input[type="text"] {
            outline: none;
            border: none;
        }

        .buttons-index input[type="text"]::placeholder {
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

        .input-container input[type="text"]::placeholder {
            font-size: 12px;
        }

        .form-control {
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

            .containers {
                height: auto;
            }

            .content {
                grid-template-columns: 1fr;
            }

            .back {
                margin-top: 100px;
            }

            .col-left {
                padding-top: 20px;
                margin-left: 0;
            }

            .col-left h2 {
                font-size: 24px;
            }

            .col-left span {
                margin: none;
            }

            .input-container input[type="text"]::placeholder {
                font-size: 10px;
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

        /* REGISTER CSS */

        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: auto;
            padding: 0px 50px;
            color: #2a2a2a;
            font-size: 14px;
            background-color: #ff9548;
        }

        .register-container p {
            font-size: 0.6rem;
            color: #2a2a2a;
        }

        .register-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .column-left {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0px;
        }

        .submit-btn {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            margin-top: 20px;
            width: 100%;
            padding: 10px 15px;
            border: none;
            background-color: #2a2a2a;
            border-radius: 10px;
        }

        .submit-btn:hover {
            color: #ffffff;
            background-color: #1c1c1c;
            transform: translateY(-3px);
        }

        .img-box {
            transition: transform 0.3s ease;
        }

        .img-box:hover {
            transform: translateY(-5px);
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Ensure the navbar takes up the full width */
        .header .navbar {
            width: 100%;
        }

        /* Add these responsive styles */
        @media (max-width: 1200px) {
            .content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .col-left,
            .col-right {
                padding: 0 20px;
            }

            .col-right img {
                max-width: 100%;
                height: auto;
            }
        }

        @media (max-width: 992px) {
            .feature {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .col-lefts,
            .col-rights,
            .col-leftss,
            .col-rightss {
                text-align: center;
            }

            .col-rights img,
            .col-rightss img {
                max-width: 100%;
                height: auto;
            }
        }

        @media (max-width: 768px) {
            .scroll-category {
                grid-template-columns: repeat(2, 1fr);
            }

            .input-group {
                flex-direction: column;
            }

            .input-container,
            .btn-index {
                width: 100%;
                margin-top: 10px;
            }

            .footer-content .row {
                flex-direction: column;
            }

            .footer-widget {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 576px) {
            .scroll-category {
                grid-template-columns: 1fr;
            }

            .img-box {
                width: 100%;
            }

            .content-box {
                grid-template-columns: 1fr;
            }

            .col-left h2 {
                font-size: 24px;
            }
        }

        /* Ensure the header is responsive */
        @media (max-width: 992px) {
            .header .navbar-nav {
                flex-direction: column;
                align-items: center;
            }

            .header .navbar-nav .nav-item {
                margin: 10px 0;
            }

            .header .d-flex {
                flex-direction: column;
                align-items: center;
            }
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
                        <li class="nav-item active" aria-current="page"><a class="nav-link" href="home.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="service.php">Services</a></li>
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



    <div class="containers">
        <div class="content">
            <div class="col-left">
                <h2>BAGOEXPESS<br>
                    Your Modernized Transport and Delivery Services</h2>
                <span>Experience reliable, convenient transport and delivery with us. Join thousands of satisfied customers today.</span>
                <div class="input-group">
                    <div class="input-container">
                        <input type="text" class="form-control" id="locationInput" placeholder="Enter street name or barangay">
                        <span class="locate-me" id="locateMe"><i class='bx bx-target-lock'></i>Locate me</span>
                    </div>
                    <button class="btn-index" type="button" id="findNowButton">Find Now</button>
                </div>
            </div>
            <div class="col-right">
                <img src="../index-bg.png" alt="Background Image">
            </div>
        </div>
    </div>

    <h6 id="travel-destination" style="text-align: left; margin-left: 50px; padding-top: 20px;">Travel Destination</h6>
    <div class="browse-btn">
        <a href="browse-more.php" style="text-align: right;">Browse More >></a>

    </div>
    <div class="scroll-category">
        <?php
        $destinations = [
            ['name' => 'MYMTCCC', 'image' => 'mymtccc.jpg'],
            ['name' => 'Javellana Mansion', 'image' => 'javellana-mansion.jpg'],
            ['name' => 'Kipot Twin Falls', 'image' => 'kipot.jpg'],
            ['name' => 'Bantayan Park', 'image' => 'bantayan.jpg'],
            ['name' => 'Buenos Aires', 'image' => 'buenos.jpg'],
            ['name' => 'Gianna\'s Mt. Resort', 'image' => 'giannas.jpg'],
            ['name' => 'Rafael Salas Park', 'image' => 'rafael.jpg'],
            ['name' => 'J Resort', 'image' => 'j.jpg']
        ];

        foreach ($destinations as $destination) {
            echo '<div class="img-box">';
            echo '<a href="destination-highlight.php?name=' . urlencode($destination['name']) . '&image=' . urlencode($destination['image']) . '">';
            echo '<img src="../image/' . $destination['image'] . '" alt="' . $destination['name'] . '">';
            echo '<div class="img-header">';
            echo '<p>' . $destination['name'] . '</p>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
        ?>
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
                <img src="../image/box1.png" class="image-box" alt="Feature1-Image">
            </div>
            <div class="col-rights">
                <h2>Shop online</h2>
                <p>Discover a world of convenience with BagoExpress's online shopping service. Easily browse, select, and have your favorite products delivered right to your doorstep, saving you time and effort.</p>
                <button class="feature-btn"><a href="shop.php">Learn more</a></button>
            </div>
        </div>
        <br>
        <div class="feature">
            <div class="col-leftss">
                <h2>Ride Hailing</h2>
                <p>Get where you need to go quickly and safely with our reliable ride-hailing service. Whether it's a daily commute or a special trip, BagoExpress ensures a comfortable journey every time.</p>
                <button class="feature-btn"><a href="service.php">Learn more</a></button>
            </div>
            <div class="col-rightss">
                <img src="../image/box2.png" class="image-box" alt="Feature2-Image">
            </div>
        </div>
        <br>
        <div class="feature">
            <div class="col-lefts">
                <img src="../image/box3.png" class="image-box" alt="Feature3-Image">
            </div>
            <div class="col-rights">
                <h2>Pabili</h2>
                <p>Need something picked up? Our Pabili service is here to help. From groceries to essentials, we'll run the errand for you, so you can focus on what matters most.</p>
                <button class="feature-btn"><a href="service.php">Learn more</a></button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const locateMe = document.getElementById('locateMe');
            const locationInput = document.getElementById('locationInput');
            const findNowButton = document.getElementById('findNowButton');

            if (!locateMe || !locationInput || !findNowButton) {
                console.error("Required elements not found.");
                return;
            }

            locateMe.addEventListener('click', function() {
                locateMe.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i>Locating...";
                getLocation();
            });

            function getLocation() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
                } else {
                    fallbackToIP();
                }
            }

            function successCallback(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                reverseGeocode(latitude, longitude);
                sendLocationToServer(latitude, longitude);
            }

            function errorCallback(error) {
                console.error("Error getting location:", error);
                fallbackToIP();
            }

            function fallbackToIP() {
                fetch('https://ipapi.co/json/')
                    .then(response => response.json())
                    .then(data => {
                        reverseGeocode(data.latitude, data.longitude);
                    })
                    .catch(error => {
                        console.error("Error with IP geolocation:", error);
                        locateMe.innerHTML = "<i class='bx bx-error-circle'></i>Location unavailable";
                    });
            }

            function reverseGeocode(latitude, longitude) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.display_name;
                        locationInput.value = address;
                        locateMe.innerHTML = "<i class='bx bx-target-lock'></i>Locate me";
                    })
                    .catch(error => {
                        console.error("Error fetching address:", error);
                        locationInput.value = `Lat: ${latitude}, Lon: ${longitude}`;
                        locateMe.innerHTML = "<i class='bx bx-target-lock'></i>Locate me";
                    });
            }

            function sendLocationToServer(latitude, longitude) {
                fetch('update_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `latitude=${latitude}&longitude=${longitude}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Location updated successfully');
                        } else {
                            console.error('Failed to update location');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            findNowButton.addEventListener('click', function() {
                const currentLocation = locationInput.value || 'your current location';

                findNowButton.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i>Searching...";
                findNowButton.disabled = true;

                // Simulate finding nearby places (replace with actual API call in production)
                setTimeout(() => {
                    const nearbyPlaces = [
                        "Local Coffee Shop",
                        "Central Park",
                        "City Library",
                        "Main Street Restaurant",
                        "Community Center"
                    ];

                    const foundPlaces = nearbyPlaces
                        .sort(() => 0.5 - Math.random())
                        .slice(0, 3);

                    let message = `Places found near ${currentLocation}:\n\n`;
                    foundPlaces.forEach((place, index) => {
                        message += `${index + 1}. ${place}\n`;
                    });

                    alert(message);

                    findNowButton.innerHTML = "Find Now";
                    findNowButton.disabled = false;
                }, 2000); // Simulating a 2-second delay for the search
            });
        });
    </script>

</body>

</html>

<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>