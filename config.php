<?php
$host = "localhost";
$user = "root"; // phpMyAdmin default
$pass = "";     // phpMyAdmin default
$db   = "digital_products_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>