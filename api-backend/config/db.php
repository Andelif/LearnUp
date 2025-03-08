<?php
$host = getenv("DB_HOST") ?: "127.0.0.1"; 
$username = "root"; 
$password = getenv("DB_PASSWORD") ?: ""; 
$database = "learnup_db"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\nHost: $host | User: $username | DB: $database");
} else {
    echo "Connected successfully!";
}

?>
