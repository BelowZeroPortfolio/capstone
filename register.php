<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("connection/dbcon.php");

function sendJsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'errors' => []];

    // Collect and sanitize form data
    $fields = ['firstname', 'lastname', 'uname', 'email', 'birthday', 'password', 'confirm-password', 'role'];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // Validation
    if (empty($data['firstname'])) $response['errors']['firstname'] = 'First name is required.';
    if (empty($data['lastname'])) $response['errors']['lastname'] = 'Last name is required.';
    if (empty($data['uname'])) $response['errors']['uname'] = 'Username is required.';
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $response['errors']['email'] = 'Valid email is required.';
    if (empty($data['birthday'])) $response['errors']['birthday'] = 'Birthday is required.';
    if (empty($data['password'])) {
        $response['errors']['password'] = 'Password is required.';
    } elseif (strlen($data['password']) < 8 || strlen($data['password']) > 20) {
        $response['errors']['password'] = 'Password must be between 8 and 20 characters.';
    }
    if ($data['password'] !== $data['confirm-password']) $response['errors']['confirm-password'] = 'Passwords do not match.';

    // Check age
    $dob = new DateTime($data['birthday']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    if ($age < 18) {
        $response['errors']['birthday'] = "You must be at least 18 years old to register.";
    }

    // Check if username or email already exists
    $stmt = $con->prepare("SELECT username, email FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $data['uname'], $data['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['username'] == $data['uname']) $response['errors']['uname'] = 'This username is already taken.';
        if ($row['email'] == $data['email']) $response['errors']['email'] = 'This email is already registered.';
    }
    $stmt->close();

    if (empty($response['errors'])) {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (firstname, lastname, username, email, birthday, password, role) VALUES (?, ?, ?, ?, ?, ?, 'user')";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", $data['firstname'], $data['lastname'], $data['uname'], $data['email'], $data['birthday'], $hash);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Registration successful. Redirecting to login page...';
        } else {
            $response['errors']['general'] = "Registration failed. Please try again. Error: " . $con->error;
        }
        $stmt->close();
    }

    sendJsonResponse($response);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join BagoExpress - Sign Up</title>
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
                <h2>Join BagoExpress</h2>
                <p>Create your account and start your journey with us!</p>
            </div>
            <div class="registration-form">
                <form id="registration-form" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                                <label for="firstname">First Name</label>
                            </div>
                            <span class="error-message" id="firstname-error"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required>
                                <label for="lastname">Last Name</label>
                            </div>
                            <span class="error-message" id="lastname-error"></span>
                        </div>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="uname" name="uname" placeholder="Username" required>
                        <label for="uname">Username</label>
                        <span class="error-message" id="uname-error"></span>
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
                    <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
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
                    url: 'register.php',
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
                            $.each(response.errors, function(field, error) {
                                $('#' + field + '-error').text(error);
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error: " + textStatus + ' : ' + errorThrown);
                        console.error("Response text: " + jqXHR.responseText);

                        let errorMessage = 'An error occurred. ';
                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            errorMessage += Object.values(jqXHR.responseJSON.errors).join(' ');
                        } else if (jqXHR.status === 0) {
                            errorMessage += 'Unable to connect to the server. Please check your internet connection.';
                        } else if (jqXHR.status == 404) {
                            errorMessage += 'Requested page not found.';
                        } else if (jqXHR.status == 500) {
                            errorMessage += 'Internal Server Error.';
                        } else if (textStatus === 'parsererror') {
                            errorMessage += 'Requested JSON parse failed.';
                        } else if (textStatus === 'timeout') {
                            errorMessage += 'Time out error.';
                        } else if (textStatus === 'abort') {
                            errorMessage += 'Ajax request aborted.';
                        } else {
                            errorMessage += 'Uncaught Error: ' + jqXHR.responseText;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                            confirmButtonColor: '#007bff'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>