<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] === "logout") {
   
    $_SESSION = array();
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    setcookie("remember_me", "", time() - 3600, "/");

    header("Location: ?page=login");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $valid_username = "Prince";
    $valid_password = "Prince@123";

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        header("Location: ?page=dashboard");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$page = $_GET['page'] ?? "login";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Auth System</title>
</head>
<body>

<?php if ($page === "login" && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)): ?>

    <h2>Login</h2>
    <form method="post" action="?page=login">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

<?php elseif ($page === "dashboard" && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>This is your protected dashboard. You can view this page because you are logged in.</p>
    <p><a href="?action=logout">Logout</a></p>

<?php else: ?>
    <?php header("Location: ?page=login"); exit(); ?>
<?php endif; ?>

</body>
</html>
