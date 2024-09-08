<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();
include_once("../connection/dbcon.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$query = "SELECT user_type, COUNT(*) as count FROM users WHERE user_type != 'admin' GROUP BY user_type";
$result = mysqli_query($con, $query);

$data = [
    'labels' => [],
    'values' => []
];

$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $data['labels'][] = ucfirst($row['user_type']);
    $data['values'][] = (int)$row['count'];
    $total += (int)$row['count'];
}

// Calculate percentages
$data['values'] = array_map(function($value) use ($total) {
    return round(($value / $total) * 100, 1);
}, $data['values']);

echo json_encode($data);