<?php
$servername = "mysql";  // Changed from "localhost" to "mysql"
$username = "root";
$password = "root";
$database = "testdb";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>