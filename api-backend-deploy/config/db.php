<?php
$host = '127.0.0.1'; // Change from "localhost" to "mysql" (Docker service name)
$username = "root"; // Default MySQL username
$password = ''; // Match the MySQL password from Docker Compose
$database = "learnup_db"; // Database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>