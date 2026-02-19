<?php
session_start();
require_once 'db_config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid name and email.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        // check if email exists
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $ins->bind_param('sss', $name, $email, $hash);
            if ($ins->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register - MyStore</title>
  <link rel="stylesheet" href="styles.css">
  <style>main{max-width:420px;margin:28px auto;padding:18px}</style>
</head>
<body>
  <div class="navbar"><div class="logo"><img src="logo.jpeg" alt="logo"></div></div>
  <main>
    <h2>Create account</h2>
    <?php if(!empty($errors)): ?>
      <div style="color:#b00020;margin-bottom:12px">
      <?php foreach($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
      </div>
    <?php endif; ?>
    <form method="post" action="">
      <div><label>Name</label><br><input type="text" name="name" required></div>
      <div><label>Email</label><br><input type="email" name="email" required></div>
      <div><label>Password</label><br><input type="password" name="password" required></div>
      <div style="margin-top:12px"><button class="btn" type="submit">Register</button></div>
    </form>
    <p style="margin-top:10px">Already have an account? <a href="login.php">Login</a></p>
  </main>
</body>
</html>
