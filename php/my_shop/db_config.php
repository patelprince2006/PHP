<?php
// Database configuration - update these values for your environment
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'myshop_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    // stop execution and show a simple message (adjust for production)
    die('Database connection failed: ' . $mysqli->connect_error);
}

// set charset
$mysqli->set_charset('utf8mb4');

?>
