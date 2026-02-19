<?php
require "config.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
<h2>Welcome, <?= htmlspecialchars($_SESSION["user"]) ?>!</h2>
<p>You are logged in.</p>
<a href="logout.php">Logout</a>
</body>
</html>
