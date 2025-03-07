<?php
$host = "mysql"; // Change "localhost" to the MySQL service name from docker-compose.yml
$username = "user"; 
$password = "password"; // Ensure this matches your MySQL root password in docker-compose
$database = "learnup_db"; 

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
