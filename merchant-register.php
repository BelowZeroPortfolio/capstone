<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("connection/dbcon.php");

function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $response = ['success' => false, 'errors' => []];

    // Basic validation and sanitization
    $required_fields = ['username', 'password', 'first_name', 'last_name', 'email', 'birthday', 'shop_name', 'address', 'phone_number', 'latitude', 'longitude'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        } else {
            $_POST[$field] = sanitize_input($_POST[$field]);
        }
    }

    // Additional validation
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (strlen($_POST['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }

    if (!preg_match('/^\d{1,12}$/', $_POST['phone_number'])) {
        $errors['phone_number'] = 'Phone number must be up to 12 digits.';
    }

    // Check if username or email exists
    if (empty($errors)) {
        $stmt = $con->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $_POST['username'], $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($row['username'] == $_POST['username']) $errors['username'] = 'Username already taken.';
            if ($row['email'] == $_POST['email']) $errors['email'] = 'Email already registered.';
        }
        $stmt->close();
    }

    // Insert new user and shop
    if (empty($errors)) {
        $con->begin_transaction();

        try {
            // Insert new user
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user_type = 'merchant';
            $sql = "INSERT INTO users (first_name, last_name, username, email, password, user_type, birthday) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "sssssss",
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['username'],
                $_POST['email'],
                $hash,
                $user_type,
                $_POST['birthday']
            );
            $stmt->execute();
            $user_id = $con->insert_id;
            $stmt->close();

            // Insert new shop
            $current_time = date('Y-m-d H:i:s');
            $default_rating = 5;
            $logo = null; // Set logo to null as it's not provided during registration
            $sql = "INSERT INTO shops (shop_name, owner_id, address, phone_number, email, rating, logo, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "sisssiidds",
                $_POST['shop_name'],
                $user_id,
                $_POST['address'],
                $_POST['phone_number'],
                $_POST['email'],
                $default_rating,
                $logo,
                $_POST['latitude'],
                $_POST['longitude'],
                $current_time
            );
            $stmt->execute();
            $stmt->close();

            $con->commit();
            $response['success'] = true;
        } catch (Exception $e) {
            $con->rollback();
            $errors['general'] = "Registration failed: " . $e->getMessage();
        }
    }

    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress Merchant Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF7622;
            --secondary-color: #FFA366;
            --text-color: #333333;
            --bg-color: #F8F9FA;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .container-register {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .registration-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }

        .card-body {
            padding: 3rem;
        }

        .logo {
            width: 120px;
            margin-bottom: 1.5rem;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 118, 34, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .form-icon {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .form-floating>.form-control {
            padding-right: 2.5rem;
        }

        #map {
            height: 400px;
            width: 100%;
        }
        
        .modal-dialog {
            max-width: 800px;
        }
    </style>

    <!-- Add SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    
    <!-- Add Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>

<body>
    <div class="container-register">
        <div class="registration-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <a href="index.php" class="btn btn-white mb-3">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                        <h2>Merchant Registration</h2>
                        <p class="mb-4">Join our platform and start growing your business today!</p>
                        <img src="image/merchant-logo.png" alt="Merchant Illustration" class="img-fluid">
                    </div>
                    <div class="col-lg-6">
                        <form id="registration-form" method="POST">
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                <label for="username">Username</label>
                                <i class="fas fa-user form-icon"></i>
                                <span class="error-message" id="username-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                                <i class="fas fa-lock form-icon"></i>
                                <span class="error-message" id="password-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                <label for="first_name">First Name</label>
                                <i class="fas fa-user-circle form-icon"></i>
                                <span class="error-message" id="first_name-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                                <label for="last_name">Last Name</label>
                                <i class="fas fa-user-circle form-icon"></i>
                                <span class="error-message" id="last_name-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                <label for="email">Email</label>
                                <i class="fas fa-envelope form-icon"></i>
                                <span class="error-message" id="email-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Birthday" required>
                                <label for="birthday">Birthday</label>
                                <i class="fas fa-calendar form-icon"></i>
                                <span class="error-message" id="birthday-error"></span>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" id="shop_name" name="shop_name" placeholder="Shop Name" required>
                                <label for="shop_name">Shop Name</label>
                                <i class="fas fa-store form-icon"></i>
                                <span class="error-message" id="shop_name-error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" required readonly>
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                                <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#addressModal">Set Address</button>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number" required maxlength="12" pattern="\d{1,12}">
                                <label for="phone_number">Phone Number (max 12 digits)</label>
                                <i class="fas fa-phone form-icon"></i>
                                <span class="error-message" id="phone_number-error"></span>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
                            <p class="text-center mb-0">Already have an account? <a href="index.php" class="text-primary">Login here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Set Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="map"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmAddress">Confirm Address</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let map, marker;
            const baguCityCoordinates = [10.5389, 122.8386]; // Coordinates for Bago City
            
            $('#addressModal').on('shown.bs.modal', function () {
                if (!map) {
                    map = L.map('map').setView(baguCityCoordinates, 13); // Set initial view to Bago City with zoom level 13
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    
                    map.on('click', function(e) {
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker(e.latlng).addTo(map);
                    });
                }
                map.invalidateSize();
            });
            
            $('#confirmAddress').on('click', function() {
                if (marker) {
                    const latlng = marker.getLatLng();
                    $('#latitude').val(latlng.lat);
                    $('#longitude').val(latlng.lng);
                    
                    // Reverse geocoding to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
                        .then(response => response.json())
                        .then(data => {
                            $('#address').val(data.display_name);
                            $('#addressModal').modal('hide');
                        });
                } else {
                    alert('Please select a location on the map.');
                }
            });

            $('#registration-form').on('submit', function(e) {
                e.preventDefault();
                $('.error-message').text('');

                $.ajax({
                    url: 'merchant-register.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful!',
                                text: 'You can now log in to your merchant account.',
                                confirmButtonColor: '#FF7622',
                                confirmButtonText: 'Go to Login'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'pages/merchant-home.php';
                                }
                            });
                        } else {
                            $.each(response.errors, function(field, message) {
                                $('#' + field + '-error').text(message);
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred. Please try again.',
                            confirmButtonColor: '#FF7622'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>