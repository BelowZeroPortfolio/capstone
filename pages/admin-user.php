<?php
session_start();
include_once("../connection/dbcon.php"); // Ensure this file exists and contains database connection logic

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch users from the database with role 'user'
$query = "SELECT user_id, username, email, user_type FROM users";
$result = mysqli_query($con, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission for editing user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $user_id = $_POST['user_id'];
        $success = false;
        $message = '';

        if ($_POST['action'] === 'edit') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $user_type = $_POST['user_type'];

            $update_query = "UPDATE users SET username = ?, email = ?, user_type = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $user_type, $user_id);
            $success = mysqli_stmt_execute($stmt);
            $message = $success ? 'User updated successfully.' : 'Failed to update user.';
        } elseif ($_POST['action'] === 'delete') {
            $delete_query = "DELETE FROM users WHERE user_id = ?";
            $stmt = mysqli_prepare($con, $delete_query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            $success = mysqli_stmt_execute($stmt);
            $message = $success ? 'User deleted successfully.' : 'Failed to delete user.';
        }

        // For AJAX requests, return JSON response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => $success, 'message' => $message]);
            exit;
        }

        // For regular form submissions, set a session message and redirect
        $_SESSION['alert_message'] = $message;
        header('Location: admin-user.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4a90e2;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar .active {
            background-color: var(--sidebar-hover);
            border-left: 4px solid var(--primary-color);
        }

        .sidebar a {
            color: #ecf0f1;
            padding: 16px 24px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: var(--sidebar-hover);
            padding-left: 30px;
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a7bc8;
        }

        #sidebarToggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            #sidebarToggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1000;
                background-color: var(--primary-color);
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
            }
        }
    </style>
</head>

<body>
    <button id="sidebarToggle" class="btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar flex flex-col h-full">
        <div class="flex-grow">
            <a href="#" class="text-xl font-bold px-4 py-6">
                </i> Admin Panel
            </a>
            <hr>
            <a href="admin-dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-table"></i> Dashboard
            </a>
            <a href="admin-user.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin-user.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="admin-report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin-report.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Report
            </a>
        </div>
        <div class="mt-auto">
            <a href="admin-settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin-settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
            <hr>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
        <div class="card">
            <div class="card-header">User Management</div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                            <td>
                                <button onclick="openEditModal(<?php echo $user['user_id']; ?>)" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="delete-user-btn btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition duration-300 ease-in-out" data-user-id="<?php echo $user['user_id']; ?>">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold mb-4">Edit User</h2>
            <form id="editForm" method="POST">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="mb-4">
                    <label for="edit_username" class="block text-gray-700 font-bold mb-2">Username</label>
                    <input type="text" id="edit_username" name="username" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="edit_email" class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="edit_role" class="block text-gray-700 font-bold mb-2">Role</label>
                    <select id="edit_role" name="user_type" class="w-full px-3 py-2 border rounded-lg">
                        <option value="customer">Customer</option>
                        <option value="merchant">Merchant</option>
                        <option value="driver">Driver</option>

                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2 transition duration-300 ease-in-out">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-300 ease-in-out">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add this hidden form for delete action -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" id="delete_user_id" name="user_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.openEditModal = function(userId) {
                const user = <?php echo json_encode($users); ?>.find(u => u.user_id == userId);
                document.getElementById('edit_user_id').value = user.user_id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_role').value = user.user_type;
                document.getElementById('editModal').classList.remove('hidden');
                document.getElementById('editModal').classList.add('flex');
            }

            window.closeEditModal = function() {
                document.getElementById('editModal').classList.remove('flex');
                document.getElementById('editModal').classList.add('hidden');
            }

            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                formData.append('action', 'edit');
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'User information has been updated successfully.',
                                confirmButtonColor: '#3085d6',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            });
                        }
                    });
            });

            window.confirmDelete = function(userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        const userIdInput = document.getElementById('delete_user_id');
                        userIdInput.value = userId;

                        const formData = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The user has been successfully deleted.',
                                    confirmButtonColor: '#3085d6',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                });
                            }
                        });
                    } else {
                        Swal.fire(
                            'Cancelled',
                            'The user was not deleted.',
                            'info'
                        );
                    }
                });
            }

            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                    document.querySelector('.main-content').classList.toggle('sidebar-active');
                });
            }

            // Add click event listeners to delete buttons
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    confirmDelete(userId);
                });
            });
        });
    </script>
</body>

</html>
