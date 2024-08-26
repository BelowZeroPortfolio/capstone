<?php
include ('../connection/dbcon.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php"); // Redirect to login or home page if not logged in
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>not yet done</h1>
</body>
</html>