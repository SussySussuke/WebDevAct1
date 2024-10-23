<?php
$servername = "localhost";
$username = "root"; // or any username you have
$password = ""; // or any password you have
$dbname = "db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
