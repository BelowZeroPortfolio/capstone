<?php
include_once("../connection/dbcon.php");
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress Services</title>
    <link rel="stylesheet" href="../css/style_service.css">
</head>

<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">
                <img href="index.php" src="../css/logo.png" alt="Logo" class="logo">
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="service.php">Services</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="buttons">
                <button id="create_account">Create Account</button>
                <button id="login" href="../index.php" >Login</button>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="services-header">
            <h1>Our Services</h1>
            <p>We offer a variety of services to meet your needs.</p>
        </div>

        <div class="services-grid">
            <!-- Example service card -->
            <div class="service-card">
                <img src="css/service_image.png" alt="Service Image" class="service-image">
                <h2 class="service-title">Service Title</h2>
                <p class="service-description">Brief description of the service.</p>
                <button class="btn btn-primary">Learn More</button>
            </div>
            <!-- Repeat the service-card div for more services -->
        </div>
    </div>
</body>

</html>
