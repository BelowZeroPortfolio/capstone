<?php
include_once("connection/dbcon.php"); // Include your database connection file

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't trim password to allow spaces

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        // Prepare a SQL statement to prevent SQL injection
        $sql = "SELECT * FROM user WHERE username = ?";
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("s", $username);

            // Execute the statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                // Check if user exists
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        // Password is correct, start a session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role']; // Assuming 'role' is a column in your user table
                        
                        // Regenerate session ID for security
                        session_regenerate_id(true);
                        
                        // Redirect based on user role
                        if ($user['role'] === 'admin') {
                            header("Location: pages/admin-dashboard.php");
                        } else {
                            header("Location: pages/home.php");
                        }
                        exit;
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "An error occurred. Please try again later.";
            }

            // Close the statement
            $stmt->close();
        } else {
            $error = "An error occurred. Please try again later.";
        }
    }

    // Close the database connection
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagoExpress Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF7622;
            --secondary-color: #ffffff;
        }
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background-color: var(--secondary-color);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .login-form {
            padding: 2.5rem;
        }
        .form-floating > .form-control {
            padding: 1rem 0.75rem;
            height: 3.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-floating > label {
            padding: 0.75rem;
            color: #757575;
            height: 3.5rem;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus,
        .form-floating > .form-control:not(:placeholder-shown) {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 118, 34, 0.2);
        }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            color: var(--primary-color);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--secondary-color);
        }
        .btn-primary:hover {
            background-color: #e66b1f;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 118, 34, 0.3);
        }
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .forgot-password:hover {
            color: #e66b1f;
            text-decoration: underline;
        }
        .signup-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .signup-link:hover {
            color: #e66b1f;
            text-decoration: underline;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="login-container w-100" style="max-width: 400px;">
            <div class="login-form">
                <h2 class="text-center mb-4">Welcome Back</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <div class="form-floating mb-3">
                        <input type="text" id="username" name="username" class="form-control" placeholder=" " maxlength="20" required>
                        <label for="username">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder=" " maxlength="20" required>
                        <label for="password">Password</label>
                    </div>
                    <div class="text-end mb-3">
                        <a href="pages/forgot-password.php" class="forgot-password">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Log In</button>
                    <div class="text-center">
                        Don't have an account? <a href="register.php" class="signup-link">Sign up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>