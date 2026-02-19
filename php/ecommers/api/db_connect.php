<?php
// api/db_connect.php

// Configuration - REPLACE WITH YOUR ACTUAL CREDENTIALS
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nutraleaf_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If DB fails, the main app.js will fall back to localStorage
    header('HTTP/1.1 503 Service Unavailable');
    die("Connection failed: " . $conn->connect_error);
}

// Set up for JSON interaction
header('Content-Type: application/json');

function clean_input($conn, $data) {
    return $conn->real_escape_string($data);
}
?>