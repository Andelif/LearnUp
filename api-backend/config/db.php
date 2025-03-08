<?php
$host = "mysql"; 
$username = "root"; 
$password = "root"; 
$database = "learnup_db"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\nHost: $host | User: $username | DB: $database");
} else {
    echo "Connected successfully!";
}

?>
