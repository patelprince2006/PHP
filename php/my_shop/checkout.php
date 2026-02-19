<?php
// Checkout endpoint: accepts JSON POST with cart, payment_method, upi_id, user
header('Content-Type: application/json; charset=utf-8');

// Ensure we always return JSON (capture any accidental HTML/PHP output)
// Start session first to ensure cookies/headers can be sent before any other output
session_start(); // <-- MOVED UP: This must be the first thing called.

// Checkout endpoint: accepts JSON POST with cart, payment_method, upi_id, user
header('Content-Type: application/json; charset=utf-8'); // <-- NOW CALLED AFTER session_start()

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data || !isset($data['cart'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

$cart = $data['cart'];
// ... rest of the checkout logic ...

// prefer server-side session values for user id/name
$session_user_id = $_SESSION['user_id'] ?? null;
$session_user_name = $_SESSION['user_name'] ?? null;
// ... rest of the code is unchanged ...
$payment = $data['payment_method'] ?? 'cod';
$upi = $data['upi_id'] ?? '';
$payment_id = $data['payment_id'] ?? null; // Razorpay Payment ID
$total = $data['total'] ?? null;

// shipping details from payload
$shipping = $data['shipping'] ?? [];
$ship_name = $shipping['name'] ?? null;
$ship_address = $shipping['address'] ?? null;
$ship_city = $shipping['city'] ?? null;
$ship_state = $shipping['state'] ?? null;
$ship_pincode = $shipping['pincode'] ?? null;
$ship_mobile = $shipping['mobile'] ?? null;

// prefer server-side session values for user id/name
$session_user_id = $_SESSION['user_id'] ?? null;
$session_user_name = $_SESSION['user_name'] ?? null;

$user_id = $session_user_id ?? ($data['user_id'] ?? null);
$user_name = $session_user_name ?? ($data['user_name'] ?? ($data['user'] ?? null));

$order_id = uniqid('ms_', true);
$created_at = date('Y-m-d H:i:s');

// Try to store order in DB using existing db_config.php (mysqli $mysqli)
// include db_config.php but capture any output (PHP warnings/errors) so we can return JSON
$dbConfigPath = __DIR__ . DIRECTORY_SEPARATOR . 'db_config.php';
if (!file_exists($dbConfigPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB configuration not found']);
    exit;
}

ob_start();
// suppress direct output but keep return value; warnings/errors will be in buffer
@$inc = include $dbConfigPath; 
$incOutput = ob_get_clean();

// if include produced HTML or text, return it as an error for debugging
if ($incOutput && trim($incOutput) !== '') {
    // strip HTML tags to avoid double-HTML but include raw text
    $txt = trim(strip_tags($incOutput));
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error including db_config.php: ' . $txt]);
    exit;
}

if (!isset($mysqli) || !($mysqli instanceof mysqli) || $mysqli->connect_errno) {
    $msg = 'Database connection not available';
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $msg .= ': ' . $mysqli->connect_error;
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// Create orders table if it doesn't exist
$createSql = "CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` VARCHAR(64) NOT NULL UNIQUE,
    `user_id` VARCHAR(64) DEFAULT NULL,
    `user_name` VARCHAR(255) DEFAULT NULL,
    `shipping_name` VARCHAR(255) DEFAULT NULL,
    `shipping_address` TEXT DEFAULT NULL,
    `shipping_city` VARCHAR(128) DEFAULT NULL,
    `shipping_state` VARCHAR(128) DEFAULT NULL,
    `shipping_pincode` VARCHAR(32) DEFAULT NULL,
    `shipping_mobile` VARCHAR(64) DEFAULT NULL,
    `cart` TEXT NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `payment_id` VARCHAR(100) DEFAULT NULL,
    `upi` VARCHAR(100) DEFAULT NULL,
    `total` VARCHAR(64) DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT 'Confirmed',
    `created_at` DATETIME NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";

if (!$mysqli->query($createSql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to prepare orders table: ' . $mysqli->error]);
    exit;
}

// Insert order using prepared statement
// Prepare insert; if columns are missing (older table schema), try to add them and retry once
$insertSql = "INSERT INTO `orders` (`order_id`,`user_id`,`user_name`,`shipping_name`,`shipping_address`,`shipping_city`,`shipping_state`,`shipping_pincode`,`shipping_mobile`,`cart`,`payment_method`,`payment_id`,`upi`,`total`,`created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

try {
    $stmt = $mysqli->prepare($insertSql);
} catch (Throwable $e) {
    $stmt = false;
    $err = $e->getMessage();
}

if (!$stmt) {
    // if error mentions Unknown column, attempt to add missing columns and retry
    $err = $err ?? $mysqli->error;
    if (stripos($err, 'Unknown column') !== false) {
        // determine current database
        $dbRow = $mysqli->query("SELECT DATABASE() as db");
        $dbName = $dbRow ? $dbRow->fetch_assoc()['db'] : null;
        if ($dbName) {
            $present = [];
            // Select ALL columns to correctly identify which ones are missing
            $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $mysqli->real_escape_string($dbName) . "' AND TABLE_NAME = 'orders'";
            $res = $mysqli->query($sql);
            if ($res) {
                while ($r = $res->fetch_assoc()) $present[] = $r['COLUMN_NAME'];
            }
            $toAdd = [];
            if (!in_array('user_id', $present)) $toAdd[] = "ADD COLUMN `user_id` VARCHAR(64) DEFAULT NULL";
            if (!in_array('user_name', $present)) $toAdd[] = "ADD COLUMN `user_name` VARCHAR(255) DEFAULT NULL";
            if (!in_array('shipping_name', $present)) $toAdd[] = "ADD COLUMN `shipping_name` VARCHAR(255) DEFAULT NULL";
            if (!in_array('shipping_address', $present)) $toAdd[] = "ADD COLUMN `shipping_address` TEXT DEFAULT NULL";
            if (!in_array('shipping_city', $present)) $toAdd[] = "ADD COLUMN `shipping_city` VARCHAR(128) DEFAULT NULL";
            if (!in_array('shipping_state', $present)) $toAdd[] = "ADD COLUMN `shipping_state` VARCHAR(128) DEFAULT NULL";
            if (!in_array('shipping_pincode', $present)) $toAdd[] = "ADD COLUMN `shipping_pincode` VARCHAR(32) DEFAULT NULL";
            if (!in_array('shipping_mobile', $present)) $toAdd[] = "ADD COLUMN `shipping_mobile` VARCHAR(64) DEFAULT NULL";
            if (!in_array('payment_id', $present)) $toAdd[] = "ADD COLUMN `payment_id` VARCHAR(100) DEFAULT NULL";

            if ($toAdd) {
                $alterSql = "ALTER TABLE `orders` " . implode(', ', $toAdd);
                if (!$mysqli->query($alterSql)){
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to alter orders table: ' . $mysqli->error]);
                    exit;
                }
            }
        }

        // retry prepare once (catch exceptions)
        try {
            $stmt = $mysqli->prepare($insertSql);
        } catch (Throwable $e) {
            $stmt = false;
        }
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Prepare failed after altering table: ' . $mysqli->error]);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $err]);
        exit;
    }
}

$cartJson = json_encode($cart, JSON_UNESCAPED_UNICODE);
$uid = is_null($user_id) ? null : (string)$user_id;
$uname = is_null($user_name) ? null : (string)$user_name;
$sname = is_null($ship_name) ? null : (string)$ship_name;
$saddr = is_null($ship_address) ? null : (string)$ship_address;
$scity = is_null($ship_city) ? null : (string)$ship_city;
$sstate = is_null($ship_state) ? null : (string)$ship_state;
$spincode = is_null($ship_pincode) ? null : (string)$ship_pincode;
$smobile = is_null($ship_mobile) ? null : (string)$ship_mobile;
$pid = is_null($payment_id) ? null : (string)$payment_id;

$stmt->bind_param('sssssssssssssss', $order_id, $uid, $uname, $sname, $saddr, $scity, $sstate, $spincode, $smobile, $cartJson, $payment, $pid, $upi, $total, $created_at);
$ok = $stmt->execute();
if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Insert failed: ' . $stmt->error]);
    exit;
}

$stmt->close();

// also append to orders.txt as a lightweight log (optional)
$ordersFile = __DIR__ . DIRECTORY_SEPARATOR . 'orders.txt';
$log = json_encode([
    'order_id' => $order_id,
    'user_id' => $user_id,
    'user_name' => $user_name,
    'shipping' => [
        'name' => $ship_name,
        'address' => $ship_address,
        'city' => $ship_city,
        'state' => $ship_state,
        'pincode' => $ship_pincode,
        'mobile' => $ship_mobile
    ],
    'cart' => $cart,
    'payment' => $payment,
    'upi' => $upi,
    'total' => $total,
    'created_at' => $created_at
]) . PHP_EOL;
file_put_contents($ordersFile, $log, FILE_APPEND | LOCK_EX);

echo json_encode(['success' => true, 'order_id' => $order_id]);
