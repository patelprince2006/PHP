<?php
require "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $message = "Error: Username or email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$username, $email, $hashed]);
                $message = "Registration successful! <a href='login.php'>Login here</a>";
            } catch (PDOException $e) {
                $message = "Database Error: " . $e->getMessage();
            }
        }
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>Register</h2>
<p style="color:green;"><?= $message ?></p>
<form method="post">
    Username: <input type="text" name="username" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
</body>
</html>
