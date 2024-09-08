<?php
session_start();
include_once("../connection/dbcon.php");

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $username = $_SESSION['username'];

    if ($latitude !== null && $longitude !== null) {
        $stmt = $con->prepare("UPDATE user SET latitude = ?, longitude = ? WHERE username = ?");
        $stmt->bind_param("dds", $latitude, $longitude, $username);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$con->close();
?>