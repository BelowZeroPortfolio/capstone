<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bagoexpress";

$con = new mysqli($servername, $username, $password, $dbname);
if($con->connect_error){
    echo "Cannot connect:" . $con->connect_error;
}
?>