<?php
$servername = "127.0.0.1:3307";
$username = "proftaak";
$password = "GS2001";
$dbname = "pop";

// Create a new MySQLi object
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}
?>
