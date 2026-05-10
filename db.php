<?php
$host = "localhost";
$user = "socialuser";
$pass = "123456";
$dbname = "socialdb";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
