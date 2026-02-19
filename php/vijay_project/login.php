<?php
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        echo "Login Successful! Welcome " . $row['fullname'];
    } else {
        echo "Wrong Password!";
    }
} else {
    echo "User not found!";
}

$conn->close();
?>
