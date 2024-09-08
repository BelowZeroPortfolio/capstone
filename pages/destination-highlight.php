<?php
include_once("../connection/dbcon.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

$name = isset($_GET['name']) ? $_GET['name'] : 'Unknown Destination';
$image = isset($_GET['image']) ? $_GET['image'] : 'default.jpg';

// You can add more details about each destination in an array or fetch from a database
$destinationDetails = [
    'MYMTCCC' => 'MYMTCCC is a beautiful destination known for...',
    'Javellana Mansion' => 'Javellana Mansion is a historic site that...',
    'Kipot Twin Falls' => 'Kipot Twin Falls offers breathtaking views of...',
    'Bantayan Park' => 'Bantayan Park is a popular recreational area where...',
    'Buenos Aires' => 'Buenos Aires is a charming location featuring...',
    'Gianna\'s Mt. Resort' => 'Gianna\'s Mt. Resort provides a relaxing getaway with...',
    'Rafael Salas Park' => 'Rafael Salas Park is known for its beautiful landscapes and...',
    'J Resort' => 'J Resort offers luxurious accommodations and activities such as...'
];

$description = isset($destinationDetails[$name]) ? $destinationDetails[$name] : 'No description available.';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name; ?> - BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $name; ?></h1>
        <img src="../image/<?php echo $image; ?>" alt="<?php echo $name; ?>">
        <p><?php echo $description; ?></p>
        <a href="home.php#travel-destination" class="back-link">&larr; Back to Home</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>