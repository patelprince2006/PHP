<?php
session_start();

$host = "localhost";
$dbname = "auth_demo";  
$dbuser = "root";        
$dbpass = "";            

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

$admin_email = "admin@example.com";
$admin_pass = "admin123"; 
if (!isset($_SESSION['is_admin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'], $_POST['password'])) {
        if ($_POST['email'] === $admin_email && $_POST['password'] === $admin_pass) {
            $_SESSION['is_admin'] = true;
        } else {
            $error = "Invalid admin login!";
        }
    } else {
        // show login form
        echo '<h2>Admin Login</h2>';
        if (!empty($error)) echo "<p style='color:red;'>$error</p>";
        echo '<form method="post">
                Email: <input type="email" name="email" required><br>
                Password: <input type="password" name="password" required><br>
                <button type="submit">Login</button>
              </form>';
        exit;
    }
}

// DELETE USER 
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// UPDATE USER
if (isset($_POST['update_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?");
    $stmt->execute([$_POST['username'], $_POST['email'], $_POST['update_id']]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// FETCH USERS 
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

?>

<h2>Admin Dashboard</h2>
<p>Welcome, Admin | <a href="?logout=1">Logout</a></p>

<?php
// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<h3>User List</h3>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['id']) ?></td>
    <td><?= htmlspecialchars($u['username']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td>
        <a href="?edit=<?= $u['id'] ?>">Edit</a> | 
        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?');">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php
// EDIT FORM
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch();
    if ($editUser):
?>
<h3>Edit User ID <?= $editUser['id'] ?></h3>
<form method="post">
    <input type="hidden" name="update_id" value="<?= $editUser['id'] ?>">
    Username: <input type="text" name="username" value="<?= htmlspecialchars($editUser['username']) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required><br>
    <button type="submit">Update</button>
</form>
<?php
    endif;
}
?>
