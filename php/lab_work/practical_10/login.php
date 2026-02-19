<?php
session_start();

$users = [
    "admin"    => ["password" => "admin@123", "role" => "admin"],
    "student1" => ["password" => "stud123",    "role" => "user"],
    "student2" => ["password" => "pass321",    "role" => "user"]
];

if (isset($_GET['action']) && $_GET['action'] === "logout") {
    session_unset();
    session_destroy();
    header("Location: ?page=login");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $users[$username]['role'];
        header("Location: ?page=dashboard");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}

$page = $_GET['page'] ?? "login";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Auth System</title>
</head>
<body>

<?php if ($page === "login" && !isset($_SESSION['username'])): ?>
    <h2>Login</h2>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p style="color:red;"><?php echo $error; ?></p>

<?php elseif ($page === "dashboard" && isset($_SESSION['username'])): ?>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Your role: <?php echo $_SESSION['role']; ?></p>
    <a href="?action=logout">Logout</a>

<?php else: ?>
    <?php header("Location: ?page=login"); exit; ?>
<?php endif; ?>

</body>
</html>
