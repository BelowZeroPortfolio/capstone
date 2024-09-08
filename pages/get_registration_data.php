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

$filter = $_GET['filter'] ?? 'weekly';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$week = $_GET['week'] ?? date('W');

function getWeekDates($year, $week) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $start = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $end = $dto->format('Y-m-d');
    return [$start, $end];
}

if ($filter === 'weekly') {
    list($startDate, $endDate) = getWeekDates($year, $week);
    $query = "SELECT DATE(created_at) as date, user_type, COUNT(*) as count 
              FROM users 
              WHERE created_at BETWEEN ? AND ?
              GROUP BY DATE(created_at), user_type
              ORDER BY DATE(created_at), user_type";
} else {
    $startDate = "$year-$month-01";
    $endDate = date('Y-m-t', strtotime($startDate));
    $query = "SELECT WEEK(created_at, 1) - WEEK('$startDate', 1) + 1 as week_number, user_type, COUNT(*) as count 
              FROM users 
              WHERE created_at BETWEEN ? AND ?
              GROUP BY week_number, user_type
              ORDER BY week_number, user_type";
}

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [
    'labels' => [],
    'datasets' => [
        'customer' => [],
        'driver' => [],
        'merchant' => []
    ],
    'period' => $filter === 'weekly' ? "Week $week, $year" : date('F Y', strtotime($startDate))
];

if ($filter === 'weekly') {
    $dateRange = new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate . ' +1 day'));
    foreach ($dateRange as $date) {
        $data['labels'][] = $date->format('D');
        $data['datasets']['customer'][] = 0;
        $data['datasets']['driver'][] = 0;
        $data['datasets']['merchant'][] = 0;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $index = array_search(date('D', strtotime($row['date'])), $data['labels']);
        if ($index !== false) {
            $data['datasets'][$row['user_type']][$index] = (int)$row['count'];
        }
    }
} else {
    for ($i = 1; $i <= 5; $i++) {
        $data['labels'][] = "Week $i";
        $data['datasets']['customer'][] = 0;
        $data['datasets']['driver'][] = 0;
        $data['datasets']['merchant'][] = 0;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $index = $row['week_number'] - 1;
        if ($index >= 0 && $index < 5) {
            $data['datasets'][$row['user_type']][$index] = (int)$row['count'];
        }
    }
}

echo json_encode($data);