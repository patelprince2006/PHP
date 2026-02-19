<?php
$localhost = "localhost";
$username = "root";
$password = "";
$database = "student";

// Create connection
$conn = new mysqli($localhost, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
}
echo "Connected successfully<br>";

// Insert 1st row
$sql1 = "INSERT INTO marks (indes, name, mark, id) VALUES (1,'ansh', 100, 92)";
if ($conn->query($sql1) == TRUE) {
    echo "Record 1 inserted successfully<br>";
} else {
    echo "Error: " . $conn->error . "<br>";
}

$conn->close();
?>
