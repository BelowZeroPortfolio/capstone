<?php
include_once("connection/dbcon.php"); // Include your database connection file

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't trim password to allow spaces

    // Debug log
    error_log("Login attempt for username: " . $username);

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $error = "Please fill in both fields.";
        error_log("Login failed: Empty fields");
    } else {
        // Prepare a SQL statement to prevent SQL injection
        $sql = "SELECT * FROM users WHERE username = ?";
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
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_type'] = $user['user_type']; // Assuming 'role' is a column in your user table
                

                        // Redirect based on user role
                        if ($user['user_type'] === 'admin') {
                            header("Location: pages/admin-dashboard.php");
                        } else if ($user['user_type'] === 'merchant') {
                            header("Location: pages/merchant-home.php");
                        } else if ($user['user_type'] === 'driver') {
                            header("Location: pages/driver-home.php");
                        } else {
                            header("Location: pages/home.php");
                        }
                        exit;
                    } else {
                        $error = "Invalid username or password.";
                        error_log("Login failed: Invalid password for user: " . $username);
                    }
                } else {
                    $error = "Invalid username or password.";
                    error_log("Login failed: User not found: " . $username);
                }
            } else {
                $error = "An error occurred. Please try again later.";
                error_log("Login failed: SQL execution error");
            }

            // Close the statement
            $stmt->close();
        } else {
            $error = "An error occurred. Please try again later.";
            error_log("Login failed: SQL prepare error");
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
    <link rel="stylesheet" href="css/login.css">
    
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