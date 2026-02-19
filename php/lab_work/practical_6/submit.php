<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration Form</title>
</head>
<body>
  <h2>Student Registration</h2>
  <form method="POST">
    <label>Full Name: <input type="text" name="fullname" required></label><br><br>
    <label>Student ID: <input type="text" name="studentid" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Username: <input type="text" name="username" required></label><br><br>
    <label>Password: <input type="password" name="password" required></label><br><br>
    <label>Phone: <input type="text" name="phone" required></label><br><br>
    <button type="submit">Register</button>
  </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname   = $_POST['fullname'];
    $studentid  =$_POST['studentid'];
    $email      =$_POST['email'];
    $username   =$_POST['username'];
    $password   =$_POST['password'];
    $phone      =$_POST['phone'];

    echo "<h2>Registration Successful!</h2>";
    echo "<p><b>Name:</b> $fullname</p>";
    echo "<p><b>Student ID:</b> $studentid</p>";
    echo "<p><b>Email:</b> $email</p>";
    echo "<p><b>Username:</b> $username</p>";
    echo "<p><b>Phone:</b> $phone</p>";
} else {
    echo "<h3>Null request</h3>";
}
?>
