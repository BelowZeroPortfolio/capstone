<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once("connection/dbcon.php");

function sendJsonResponse($data)
{
    $output = ob_get_clean();
    if (!empty($output)) {
        $data['debug_output'] = $output;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $response = ['success' => false, 'errors' => []];

        // Collect and sanitize form data
        $fields = ['first_name', 'last_name', 'username', 'email', 'birthday', 'password', 'confirm-password', 'vehicle_number', 'phone_number'];
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // Validation
        if (empty($data['first_name'])) $response['errors']['first_name'] = 'First name is required.';
        if (empty($data['last_name'])) $response['errors']['last_name'] = 'Last name is required.';
        if (empty($data['username'])) $response['errors']['username'] = 'Username is required.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $response['errors']['email'] = 'Valid email is required.';
        if (empty($data['birthday'])) $response['errors']['birthday'] = 'Birthday is required.';
        if (empty($data['password'])) {
            $response['errors']['password'] = 'Password is required.';
        } elseif (strlen($data['password']) < 8 || strlen($data['password']) > 20) {
            $response['errors']['password'] = 'Password must be between 8 and 20 characters.';
        }
        if ($data['password'] !== $data['confirm-password']) $response['errors']['confirm-password'] = 'Passwords do not match.';
        if (empty($data['vehicle_number']) || !preg_match('/^[A-Z]{3}-\d{4}$/', $data['vehicle_number'])) {
            $response['errors']['vehicle_number'] = 'Vehicle number must be in the format ABC-1234.';
        }
        if (empty($data['phone_number']) || !preg_match('/^\d{11}$/', $data['phone_number'])) {
            $response['errors']['phone_number'] = 'Phone number must be 11 digits.';
        }

        // Check age
        $dob = new DateTime($data['birthday']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        if ($age < 18) {
            $response['errors']['birthday'] = "You must be at least 18 years old to register.";
        }

        // Check if username or email already exists
        $stmt = $con->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $data['username'], $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($row['username'] == $data['username']) $response['errors']['username'] = 'This username is already taken.';
            if ($row['email'] == $data['email']) $response['errors']['email'] = 'This email is already registered.';
        }
        $stmt->close();

        if (empty($response['errors'])) {
            $con->begin_transaction();

            try {
                // Insert into users table
                $hash = password_hash($data['password'], PASSWORD_DEFAULT);
                $user_type = 'driver';
                $created_at = date('Y-m-d H:i:s');
                $sql = "INSERT INTO users (first_name, last_name, username, email, password, user_type, birthday, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssssssss", $data['first_name'], $data['last_name'], $data['username'], $data['email'], $hash, $user_type, $data['birthday'], $created_at);
                $stmt->execute();
                $user_id = $con->insert_id;
                $stmt->close();

                // Insert into drivers table
                $sql = "INSERT INTO drivers (user_id, vehicle_number, phone_number) VALUES (?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("iss", $user_id, $data['vehicle_number'], $data['phone_number']);
                $stmt->execute();
                $stmt->close();

                $con->commit();
                $response['success'] = true;
                $response['message'] = 'Registration successful. Redirecting to login page...';
            } catch (Exception $e) {
                $con->rollback();
                $response['errors']['general'] = "Registration failed. Please try again. Error: " . $e->getMessage();
            }
        }

        sendJsonResponse($response);
    }
} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'errors' => ['general' => 'An unexpected error occurred.'],
        'debug_output' => $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress Driver Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .registration-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .registration-header {
            background-color: #FF7622;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .registration-form {
            padding: 30px;
        }

        .form-floating>.form-control {
            height: calc(2.5rem + 2px);
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-floating>.form-control:focus,
        .form-floating>.form-control:not(:placeholder-shown) {
            padding-top: 1.25rem;
            padding-bottom: 0.25rem;
        }

        .form-floating>label {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .form-control:focus {
            border-color: #FF7622;
            box-shadow: 0 0 0 0.2rem rgba(255, 118, 34, 0.25);
        }

        .btn-primary {
            background-color: #FF7622;
            border-color: #FF7622;
        }

        .btn-primary:hover {
            background-color: #FF7622;
            border-color: #FF7622;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.75em;
            margin-top: 0.25rem;
        }

        .back-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-size: 24px;
            text-decoration: none;
        }

        .back-icon:hover {
            color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="registration-container">
            <a href="index.php" class="back-icon">
                <i class="bi bi-arrow-left-circle"></i>
            </a>
            <div class="registration-header">
                <h2>Join BagoExpress as a Driver</h2>
                <p>Start your journey with us today!</p>
            </div>
            <div class="registration-form">
                <form id="registration-form" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                <label for="first_name">First Name</label>
                            </div>
                            <span class="error-message" id="first_name-error"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                                <label for="last_name">Last Name</label>
                            </div>
                            <span class="error-message" id="last_name-error"></span>
                        </div>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        <label for="username">Username</label>
                        <span class="error-message" id="username-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <label for="email">Email</label>
                        <span class="error-message" id="email-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Birthday" required>
                        <label for="birthday">Birthday</label>
                        <span class="error-message" id="birthday-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <span class="error-message" id="password-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
                        <label for="confirm-password">Confirm Password</label>
                        <span class="error-message" id="confirm-password-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Vehicle Number (e.g., POB-1234)" required pattern="[A-Z]{3}-\d{4}">
                        <label for="vehicle_number">Vehicle Number</label>
                        <span class="error-message" id="vehicle_number-error"></span>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Phone Number" required pattern="\d{11}">
                        <label for="phone_number">Phone Number</label>
                        <span class="error-message" id="phone_number-error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Register as Driver</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#registration-form').on('submit', function(e) {
                e.preventDefault();
                $('.error-message').text('');

                $.ajax({
                    url: 'driver-register.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful!',
                                text: response.message,
                                confirmButtonColor: '#007bff'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php';
                                }
                            });
                        } else {
                            // Display errors in respective spans
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    $('#' + key + '-error').text(response.errors[key]);
                                });
                            }
                            // If there's a general error, display it using SweetAlert
                            if (response.errors && response.errors.general) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Registration Failed',
                                    text: response.errors.general,
                                    confirmButtonColor: '#007bff'
                                });
                            }
                        }
                        if (response.debug_output) {
                            console.error("Debug output:", response.debug_output);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown);
                        console.error("Response text:", jqXHR.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while processing your request. Please check the console for details.',
                            confirmButtonColor: '#007bff'
                        });
                    }
                });
            });

            $('#vehicle_number').on('input', function() {
                var input = $(this);
                var re = /^[A-Z]{3}-\d{4}$/;
                var is_valid = re.test(input.val());
                if(is_valid) {
                    input.removeClass("is-invalid").addClass("is-valid");
                    $('#vehicle_number-error').text('');
                } else {
                    input.removeClass("is-valid").addClass("is-invalid");
                    $('#vehicle_number-error').text('Vehicle number must be in the format ABC-1234.');
                }
            });

            $('#phone_number').on('input', function() {
                var input = $(this);
                var re = /^\d{11}$/;
                var is_valid = re.test(input.val());
                if(is_valid) {
                    input.removeClass("is-invalid").addClass("is-valid");
                    $('#phone_number-error').text('');
                } else {
                    input.removeClass("is-valid").addClass("is-invalid");
                    $('#phone_number-error').text('Phone number must be 11 digits.');
                }
            });
        });
    </script>
</body>

</html>