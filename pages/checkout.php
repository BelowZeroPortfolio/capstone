<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

$orders = isset($_POST['orders']) ? json_decode($_POST['orders'], true) : [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BagoExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .container {
            display: flex;
            justify-content: space-between;
        }

        .left-column, .right-column {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .left-column {
            width: 60%;
        }

        .right-column {
            width: 35%;
        }

        .order-summary h3 {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .order-item p {
            margin: 0;
        }

        .order-total {
            font-weight: bold;
            margin-top: 20px;
        }

        .delivery-address, .delivery-options, .personal-details {
            margin-bottom: 20px;
        }

        .delivery-address h4, .delivery-options h4, .personal-details h4 {
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        #map {
            height: 200px;
            background-color: #eee;
            margin-bottom: 10px;
        }

        .edit-button {
            display: inline-block;
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-column">
            <div class="delivery-address">
                <h4>Delivery address</h4>
                <div id="map"></div>
                <p>Davee's Park and Carwash, Bacolod City Negros Occidental</p>
                <button class="edit-button" onclick="enableMapEdit()">Edit</button>
                <form>
                    <div class="form-group">
                        <label for="street">Street</label>
                        <input type="text" id="street" name="street">
                    </div>
                    <div class="form-group">
                        <label for="floor">Floor</label>
                        <input type="text" id="floor" name="floor">
                    </div>
                    <div class="form-group">
                        <label for="note">Note to rider</label>
                        <input type="text" id="note" name="note">
                    </div>
                    <div class="form-group">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
            
            <div class="personal-details">
                <h4>Personal details</h4>
                <form>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="first_name">First name</label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last name</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    <div class="form-group">
                        <label for="mobile_number">Mobile number</label>
                        <input type="text" id="mobile_number" name="mobile_number">
                    </div>
                    <div class="form-group">
                        <button type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="right-column">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <p><?php echo htmlspecialchars($order['productName']); ?> (x<?php echo $order['quantity']; ?>)</p>
                            <p>₱<?php echo number_format($order['productPrice'] * $order['quantity'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-total">
                        <p>Total: ₱<?php echo number_format(array_sum(array_map(function($order) {
                            return $order['productPrice'] * $order['quantity'];
                        }, $orders)), 2); ?></p>
                    </div>
                <?php else: ?>
                    <p>No items in the cart.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([10.6765, 122.9509], 13); // Coordinates for Bacolod City

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([10.6765, 122.9509]).addTo(map)
            .bindPopup('Davee\'s Park and Carwash, Bacolod City Negros Occidental')
            .openPopup();

        function enableMapEdit() {
            map.on('click', function(e) {
                marker.setLatLng(e.latlng)
                    .bindPopup('New Delivery Address')
                    .openPopup();
            });
        }
    </script>
</body>
</html>