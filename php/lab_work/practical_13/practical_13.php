<?php
// secure_auth.php
session_start();

// ---- CONFIG ----
$host = "localhost";
$dbname = "auth_demo";    // your DB name
$dbuser = "root";         // default XAMPP
$dbpass = "";             // default XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// ---- REGISTER USER ----
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already exists!";
        } else {
            // Hash password securely
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert with prepared statement
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}

// ---- LOGIN USER ----
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $success = "Welcome, " . htmlspecialchars($user['username']) . "!";
    } else {
        $error = "Invalid email or password!";
    }
}

// ---- LOGOUT ----
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Auth System</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { border:1px solid #ccc; padding:20px; margin-bottom:20px; width:300px; }
        .msg { padding:10px; margin-bottom:10px; }
        .error { background:#fdd; color:#900; }
        .success { background:#dfd; color:#060; }
    </style>
    <script>
    // ---- Client-side Validation ----
    function validateRegister() {
        let pass = document.getElementById("reg_pass").value;
        if (pass.length < 6) {
            alert("Password must be at least 6 characters!");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>

<h2>Secure Auth System</h2>

<?php if (!empty($error)): ?>
    <div class="msg error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="msg success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!isset($_SESSION['user_id'])): ?>

<!-- Registration Form -->
<form method="post" onsubmit="return validateRegister();">
    <h3>Register</h3>
    Username: <input type="text" name="username" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" id="reg_pass" name="password" required><br><br>
    <button type="submit" name="register">Register</button>
</form>

<!-- Login Form -->
<form method="post">
    <h3>Login</h3>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<?php else: ?>
    <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
    <p><a href="?logout=1">Logout</a></p>
<?php endif; ?>

</body>
</html>
