<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

$userId = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');

    if (!$name || !$address || !$mobile) {
        $msg = 'Please provide name, address and mobile.';
    } else {
        // create table if not exists
        $createSql = "CREATE TABLE IF NOT EXISTS `user_profiles` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL UNIQUE,
            `name` VARCHAR(255) DEFAULT NULL,
            `address` TEXT DEFAULT NULL,
            `city` VARCHAR(128) DEFAULT NULL,
            `state` VARCHAR(128) DEFAULT NULL,
            `pincode` VARCHAR(32) DEFAULT NULL,
            `mobile` VARCHAR(64) DEFAULT NULL
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        $mysqli->query($createSql);

        // insert or update
        $stmt = $mysqli->prepare('INSERT INTO user_profiles (user_id,name,address,city,state,pincode,mobile) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), address=VALUES(address), city=VALUES(city), state=VALUES(state), pincode=VALUES(pincode), mobile=VALUES(mobile)');
        if ($stmt) {
            $stmt->bind_param('issssss', $userId, $name, $address, $city, $state, $pincode, $mobile);
            if ($stmt->execute()) {
                $msg = 'Saved.';
            } else {
                $msg = 'Save failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = 'DB error: ' . $mysqli->error;
        }
    }
}

// load existing
$profile = ['name'=>'','address'=>'','city'=>'','state'=>'','pincode'=>'','mobile'=>''];
$stmt = $mysqli->prepare('SELECT name,address,city,state,pincode,mobile FROM user_profiles WHERE user_id = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($p_name,$p_address,$p_city,$p_state,$p_pincode,$p_mobile);
    if ($stmt->fetch()) {
        $profile = ['name'=>$p_name,'address'=>$p_address,'city'=>$p_city,'state'=>$p_state,'pincode'=>$p_pincode,'mobile'=>$p_mobile];
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profile - MyStore</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="navbar"><div class="logo"><img src="logo.jpeg" alt="logo"></div></div>
  <main style="max-width:600px;margin:20px auto;padding:18px">
    <h2>Saved Shipping Address</h2>
    <?php if($msg): ?><div style="color:green;margin-bottom:10px"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <form method="post">
      <div><input name="name" placeholder="Full name" required style="width:100%;padding:8px" value="<?php echo htmlspecialchars($profile['name']); ?>"></div>
      <div style="margin-top:8px"><textarea name="address" placeholder="Address" required style="width:100%;padding:8px" rows="4"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea></div>
      <div style="display:flex;gap:8px;margin-top:8px">
        <input name="city" placeholder="City" style="flex:1;padding:8px" value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
        <input name="state" placeholder="State" style="flex:1;padding:8px" value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
      </div>
      <div style="display:flex;gap:8px;margin-top:8px">
        <input name="pincode" placeholder="Pincode" style="width:160px;padding:8px" value="<?php echo htmlspecialchars($profile['pincode'] ?? ''); ?>">
        <input name="mobile" placeholder="Mobile" required style="flex:1;padding:8px" value="<?php echo htmlspecialchars($profile['mobile'] ?? ''); ?>">
      </div>
      <div style="margin-top:12px"><button class="btn" type="submit">Save</button> <a href="index.php" style="margin-left:12px">Back to shop</a></div>
    </form>
  </main>
</body>
</html>
