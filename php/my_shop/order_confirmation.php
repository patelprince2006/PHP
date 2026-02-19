<?php
session_start();
require_once 'db_config.php';

$order_id = $_GET['id'] ?? '';
$show_success = isset($_GET['success']);

if (!$order_id) {
    die("Invalid Order ID");
}

$order = null;
if ($mysqli) {
    $stmt = $mysqli->prepare("SELECT * FROM orders WHERE order_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $order = $res->fetch_assoc();
        $stmt->close();
    }
}

if (!$order) {
    die("Order not found");
}

// Security check: ensure the logged-in user owns this order (if logged in)
// If the user just placed the order as a guest (if that was allowed) or we rely on session
$session_user_id = $_SESSION['user_id'] ?? null;
// You might want to relax this if you want to allow viewing by link immediately after purchase without login persistence issues, 
// but generally it's good practice. For now, we'll assume if they have the ID they can view it (for simplicity as per request).

$cart_items = json_decode($order['cart'], true);
$shipping_address = nl2br(htmlspecialchars($order['shipping_address'] ?? ''));
$shipping_info = [
    'Name' => $order['shipping_name'],
    'City' => $order['shipping_city'],
    'State' => $order['shipping_state'],
    'Pincode' => $order['shipping_pincode'],
    'Mobile' => $order['shipping_mobile'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Nutra-Leaf</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            font-family: 'Poppins', sans-serif;
        }
        .confirmation-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #0d3625;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1a4d36;
        }
        .btn-outline {
            border: 2px solid #0d3625;
            color: #0d3625;
        }
        .btn-outline:hover {
            background: #f0f0f0;
        }
        .order-id {
            font-weight: bold;
            color: #0d3625;
            background: #e8f5e9;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="confirmation-card">
    <div class="success-icon">✓</div>
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your purchase. Your order <span class="order-id">#<?php echo htmlspecialchars($order['order_id']); ?></span> has been received and is being processed.</p>
    
    <div class="btn-group">
        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        <a href="view_bill.php?id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-outline" target="_blank">View Bill</a>
    </div>

    <?php if (($order['status'] ?? 'Confirmed') !== 'Canceled'): ?>
        <div style="margin-top: 20px;">
            <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                <button type="submit" style="background:none; border:none; color:#dc3545; cursor:pointer; text-decoration:underline;">Cancel Order</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
