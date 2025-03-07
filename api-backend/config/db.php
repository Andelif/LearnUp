<?php
$host = "localhost"; // Your database host
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password is empty
$database = "learnup_db"; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>