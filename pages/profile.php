<?php
session_start();

// Simulating user data (replace with database fetch in a real application)
$user = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '123-456-7890',
    'profile_picture' => 'default.jpg'
];

$success_message = '';
$error_messages = [];

// Add CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Security check failed');
    }

    // Validate and process form submission
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($first_name) || strlen($first_name) > 50) {
        $error_messages['first_name'] = 'First name is required and must be less than 50 characters.';
    }

    if (empty($last_name) || strlen($last_name) > 50) {
        $error_messages['last_name'] = 'Last name is required and must be less than 50 characters.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages['email'] = 'Please enter a valid email address.';
    }

    if (!empty($phone) && !preg_match('/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/', $phone)) {
        $error_messages['phone'] = 'Please enter a valid phone number (e.g., 123-456-7890).';
    }

    // Basic password hashing
    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $error_messages['password'] = 'Passwords do not match.';
        }
    }

    // Process profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_info = getimagesize($_FILES['profile_picture']['tmp_name']);
        if ($file_info === false) {
            $error_messages['profile_picture'] = 'Invalid image file.';
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file_info['mime'], $allowed_types)) {
                $error_messages['profile_picture'] = 'Invalid file type. Please upload a JPEG, PNG, or GIF image.';
            } else {
                $upload_dir = 'uploads/';
                $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                    $user['profile_picture'] = $upload_path;
                } else {
                    $error_messages['profile_picture'] = 'Failed to upload profile picture. Please try again.';
                }
            }
        }
    }

    if (empty($error_messages)) {
        // Update user data (in a real app, save to database)
        $user['first_name'] = htmlspecialchars($first_name);
        $user['last_name'] = htmlspecialchars($last_name);
        $user['email'] = htmlspecialchars($email);
        $user['phone'] = htmlspecialchars($phone);
        if (isset($hashed_password)) {
            $user['password'] = $hashed_password;
        }

        $success_message = 'Profile updated successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            color: #1c1e21;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
            padding: 3rem;
            margin-top: 3rem;
        }
        .profile-picture-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 2.5rem;
        }
        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        }
        .profile-picture-container:hover .profile-picture {
            filter: brightness(0.8);
        }
        .profile-picture-overlay i {
            color: #fff;
            font-size: 2.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #65676b;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #dddfe2;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 0 0 2px rgba(24,119,242,0.2);
            border-color: #1877f2;
        }
        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #1877f2;
            border-color: #1877f2;
        }
        .btn-primary:hover {
            background-color: #166fe5;
            border-color: #166fe5;
        }
        .btn-secondary {
            background-color: #f0f2f5;
            border-color: #f0f2f5;
            color: #1c1e21;
        }
        .btn-secondary:hover {
            background-color: #e4e6eb;
            border-color: #e4e6eb;
            color: #1c1e21;
        }
        .password-toggle {
            cursor: pointer;
            background-color: #f0f2f5;
            border: none;
            color: #65676b;
        }
        .alert {
            border-radius: 10px;
            font-weight: 500;
        }
        h1 {
            color: #1c1e21;
            font-weight: 700;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="profile-container">
                    <h1 class="text-center">Edit Your Profile</h1>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="profile-picture-container mb-4">
                            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                            <label for="profile_picture" class="profile-picture-overlay">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="d-none">
                        </div>
                        <?php if (isset($error_messages['profile_picture'])): ?>
                            <div class="text-danger text-center mb-3"><?php echo $error_messages['profile_picture']; ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required maxlength="50">
                                <?php if (isset($error_messages['first_name'])): ?>
                                    <div class="text-danger"><?php echo $error_messages['first_name']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required maxlength="50">
                                <?php if (isset($error_messages['last_name'])): ?>
                                    <div class="text-danger"><?php echo $error_messages['last_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            <?php if (isset($error_messages['email'])): ?>
                                <div class="text-danger"><?php echo $error_messages['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="123-456-7890">
                            <?php if (isset($error_messages['phone'])): ?>
                                <div class="text-danger"><?php echo $error_messages['phone']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <span class="input-group-text password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            <?php if (isset($error_messages['password'])): ?>
                                <div class="text-danger"><?php echo $error_messages['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <a href="profile.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide password
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        // Preview profile picture
        document.querySelector('#profile_picture').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>