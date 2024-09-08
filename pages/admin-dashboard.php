<?php
session_start();
include_once("../connection/dbcon.php"); // Ensure this file exists and contains database connection logic

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch users from the database with role 'user'
$query = "SELECT user_id, username, email, user_type FROM users WHERE user_type = 'customer'";
$result = mysqli_query($con, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission for editing user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    $update_query = "UPDATE users SET username = ?, email = ?, user_type = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $user_type, $user_id);
    mysqli_stmt_execute($stmt);

    // Redirect to refresh the page
    header('Location: admin-home.php');
    exit();
}

// Fetch key metrics
$total_users_query = "SELECT COUNT(*) as total FROM user";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            // Count users by user_type
            $user_counts = [
                'customer' => 0,
                'merchant' => 0,
                'driver' => 0,
                'total' => 0
            ];

            $query = "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type";
            $result = mysqli_query($con, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                if (array_key_exists($row['user_type'], $user_counts)) {
                    $user_counts[$row['user_type']] = $row['count'];
                }
                $user_counts['total'] += $row['count'];
            }
            ?>

            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-2">Total Users</h2>
                <p class="text-3xl font-bold text-green-600"><?php echo $user_counts['total']; ?></p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-2">Customers</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $user_counts['customer']; ?></p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-2">Merchants</h2>
                <p class="text-3xl font-bold text-red-600"><?php echo $user_counts['merchant']; ?></p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-2">Drivers</h2>
                <p class="text-3xl font-bold text-yellow-600"><?php echo $user_counts['driver']; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-8 mt-8">
            <!-- Left Column: Bar Chart (3/4 width) -->
            <div class="col-span-3 card p-6">
                <h2 class="text-xl font-semibold mb-4">User Registrations</h2>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <select id="timeFilter" class="border rounded p-2 text-sm mr-2">
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <input type="month" id="monthPicker" class="border rounded p-2 text-sm" style="display: none;">
                        <input type="week" id="weekPicker" class="border rounded p-2 text-sm">
                    </div>
                    <div class="text-sm text-gray-500">
                        Total Registrations: <span id="totalRegistrations" class="font-semibold"></span>
                    </div>
                </div>
                <div class="h-96">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>

            <!-- Right Column: Pie Chart (1/4 width) -->
            <div class="col-span-1 card p-6">
                <h2 class="text-sm font-semibold mb-4">User Type Distribution</h2>
                <div class="flex flex-col items-center">
                    <div class="w-full h-48">
                        <canvas id="userTypeChart"></canvas>
                    </div>
                    <div id="userTypeLegend" class="mt-4 w-full"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        const userTypeColors = {
            customer: 'rgba(74, 144, 226, 0.6)',  // Blue
            merchant: 'rgba(225, 87, 89, 0.6)',   // Red
            driver: 'rgba(242, 142, 44, 0.6)'     // Orange
        };

        const userTypeColorsBorder = {
            customer: 'rgb(74, 144, 226)',  // Solid Blue
            merchant: 'rgb(225, 87, 89)',   // Solid Red
            driver: 'rgb(242, 142, 44)'     // Solid Orange
        };

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        function fetchRegistrationData(filter, year, month, week) {
            const params = new URLSearchParams({ filter, year, month, week });
            return fetch(`get_registration_data.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data;
                });
        }

        let chart;
        function updateChart(filter, year, month, week) {
            fetchRegistrationData(filter, year, month, week).then(data => {
                const ctx = document.getElementById('registrationChart').getContext('2d');
                
                if (chart) {
                    chart.destroy();
                }

                const totalRegistrations = Object.values(data.datasets).reduce((total, dataset) => 
                    total + dataset.reduce((sum, value) => sum + value, 0), 0);
                document.getElementById('totalRegistrations').textContent = totalRegistrations;

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: Object.entries(data.datasets).map(([key, value]) => ({
                            label: key.charAt(0).toUpperCase() + key.slice(1),
                            data: value,
                            backgroundColor: userTypeColors[key],
                            borderColor: userTypeColorsBorder[key],
                            borderWidth: 1
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                padding: 10,
                                cornerRadius: 4,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: data.period,
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: filter === 'weekly' ? 'Day of Week' : 'Week of Month',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Registrations',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }).catch(error => {
                console.error('Error fetching registration data:', error);
                document.getElementById('registrationChart').innerHTML = `<p class="text-red-500">Error loading chart: ${error.message}</p>`;
            });
        }

        function getCurrentWeek() {
            const now = new Date();
            const onejan = new Date(now.getFullYear(), 0, 1);
            const week = Math.ceil((((now - onejan) / 86400000) + onejan.getDay() + 1) / 7);
            return `${now.getFullYear()}-W${week.toString().padStart(2, '0')}`;
        }

        function fetchUserTypeData() {
            return fetch('get_user_type_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data;
                });
        }

        function createPieChart(data) {
            const ctx = document.getElementById('userTypeChart').getContext('2d');
            const colors = Object.values(userTypeColors);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: colors,
                        borderColor: Object.values(userTypeColorsBorder),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        datalabels: {
                            color: '#ffffff',
                            textShadowColor: 'rgba(0, 0, 0, 0.5)',
                            textShadowBlur: 5,
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (value, ctx) => {
                                let label = ctx.chart.data.labels[ctx.dataIndex];
                                return `${label}\n${value}%`;
                            },
                            align: 'center',
                            anchor: 'center'
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Remove the legend creation code
            document.getElementById('userTypeLegend').innerHTML = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const timeFilter = document.getElementById('timeFilter');
            const monthPicker = document.getElementById('monthPicker');
            const weekPicker = document.getElementById('weekPicker');

            timeFilter.addEventListener('change', updateView);
            monthPicker.addEventListener('change', updateView);
            weekPicker.addEventListener('change', updateView);

            monthPicker.value = new Date().toISOString().slice(0, 7);
            weekPicker.value = getCurrentWeek();

            function updateView() {
                const filter = timeFilter.value;
                monthPicker.style.display = filter === 'monthly' ? 'inline-block' : 'none';
                weekPicker.style.display = filter === 'weekly' ? 'inline-block' : 'none';

                let year, month, week;
                if (filter === 'monthly') {
                    [year, month] = monthPicker.value.split('-');
                } else {
                    [year, week] = weekPicker.value.split('-W');
                }

                updateChart(filter, year, month, week);
            }

            updateView();

            // Create pie chart
            fetchUserTypeData().then(createPieChart).catch(error => {
                console.error('Error fetching user type data:', error);
                document.getElementById('userTypeChart').innerHTML = `<p class="text-red-500">Error loading chart: ${error.message}</p>`;
            });
        });
    </script>
</body>

</html>