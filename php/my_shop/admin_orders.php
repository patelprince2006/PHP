<?php
// Simple admin orders viewer. Access restricted to user_id==1 or username 'admin'.
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo 'Access denied (not logged in)'; exit;
}
$uid = $_SESSION['user_id'];
$uname = $_SESSION['user_name'] ?? '';
if (!($uid == 1 || strtolower($uname) === 'admin')) {
    echo 'Access denied'; exit;
}

$res = $mysqli->query('SELECT * FROM orders ORDER BY id DESC');

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Orders - Nutra-Leaf Admin</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #eee}
    /* Styles for Nutra-Leaf header */
    .navbar {
        background: #0d3625; /* Dark Green from image */
        color: #fff;
        padding: 10px 18px;
    }
    .navbar .logo img {
        height: 30px; 
    }
    .navbar a {
        color: #fff;
        text-decoration: none;
        margin-left: 15px;
    }
  </style>
</head>
<body>
  <div class="navbar"><div class="logo"><img src="logo.jpeg" alt="logo"></div></div>
  <main style="padding:18px;max-width:1200px;margin:20px auto">
    <h2>Orders</h2>
    <p><a href="export_orders.php">Export CSV</a> | <a href="export_orders_xlsx.php">Export XLSX</a></p>
    <table>
      <thead><tr><th>ID</th><th>Order ID</th><th>User</th><th>Shipping</th><th>Cart</th><th>Payment</th><th>Total</th><th>Created</th></tr></thead>
      <tbody>
<?php
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $ship = [];
        if ($row['shipping_name'] || $row['shipping_address']) {
            $ship[] = htmlspecialchars($row['shipping_name'] ?? '');
            $ship[] = nl2br(htmlspecialchars($row['shipping_address'] ?? ''));
            $ship[] = htmlspecialchars(($row['shipping_city'] ?? '') . ' ' . ($row['shipping_pincode'] ?? ''));
            $ship[] = 'Mobile: ' . htmlspecialchars($row['shipping_mobile'] ?? '');
        }
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['order_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['user_name'] ?? $row['user_id']) . '</td>';
        echo '<td>' . implode('<br>', $ship) . '</td>';
        echo '<td>' . nl2br(htmlspecialchars($row['cart'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['payment_method']) . ($row['upi'] ? ' (UPI: '.htmlspecialchars($row['upi']).')' : '') . '</td>';
        echo '<td>' . htmlspecialchars($row['total']) . '</td>';
        echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
        echo '</tr>';
    }
}
?>
      </tbody>
    </table>
  </main>
</body>
</html>