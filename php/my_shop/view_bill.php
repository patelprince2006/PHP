<?php
session_start();
require_once 'db_config.php';

$order_id = $_GET['id'] ?? '';

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
    <title>Invoice #<?php echo htmlspecialchars($order_id); ?> - Nutra-Leaf</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f4;
            padding-top: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto 40px;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d3625;
            margin: 0;
        }
        .order-meta {
            text-align: right;
            color: #666;
        }
        .section-title {
            font-size: 1.1em;
            color: #0d3625;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 30px;
            font-weight: bold;
        }
        .shipping-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f9f9f9;
            color: #555;
            font-weight: 600;
        }
        .total-row td {
            font-weight: bold;
            font-size: 1.1em;
            color: #0d3625;
            border-top: 2px solid #eee;
        }
        .btn-print {
            background: #0d3625;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 30px;
        }
        .btn-print:hover {
            background: #1a4d36;
        }
        .home-link {
            display: inline-block;
            margin-top: 30px;
            margin-left: 20px;
            color: #0d3625;
            text-decoration: none;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-container { box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
            .btn-print, .home-link { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <div>
            <h1>INVOICE</h1>
            <p style="color:#666; margin-top:5px;">Nutra-Leaf Wellness</p>
        </div>
        <div class="order-meta">
            <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
            <p><strong>Status:</strong> 
                <?php 
                    $status = $order['status'] ?? 'Confirmed';
                    $color = ($status === 'Canceled') ? 'red' : 'green';
                    echo "<span style='color:$color; font-weight:bold'>" . htmlspecialchars($status) . "</span>";
                ?>
            </p>
        </div>
    </div>

    <div class="shipping-grid">
        <div>
            <div class="section-title">Bill To</div>
            <p><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong></p>
            <p><?php echo $shipping_address; ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' - ' . $order['shipping_pincode']); ?></p>
            <p>Mobile: <?php echo htmlspecialchars($order['shipping_mobile']); ?></p>
        </div>
        <div style="text-align: right;">
            <div class="section-title">Payment Method</div>
            <p><?php echo strtoupper(htmlspecialchars($order['payment_method'])); ?></p>
            <?php if (!empty($order['upi'])): ?>
                <p><small>UPI: <?php echo htmlspecialchars($order['upi']); ?></small></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-title">Order Summary</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subtotal = 0;
            if (is_array($cart_items)): 
                foreach ($cart_items as $item): 
                    $price = floatval($item['price'] ?? 0);
                    $qty = intval($item['qty'] ?? 1);
                    $line_total = $price * $qty;
                    $subtotal += $line_total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>₹<?php echo number_format($price, 2); ?></td>
                <td><?php echo $qty; ?></td>
                <td>₹<?php echo number_format($line_total, 2); ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
        <tfoot>
            <?php
                $tax = $subtotal * 0.05;
                $shipping = ($subtotal >= 100) ? 0 : 5;
                $grand_total = $subtotal + $tax + $shipping;
            ?>
            <tr>
                <td colspan="3" style="text-align:right; border-bottom:none; padding-top:20px;">Subtotal:</td>
                <td style="border-bottom:none; padding-top:20px;">₹<?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right; border-bottom:none;">Tax (5%):</td>
                <td style="border-bottom:none;">₹<?php echo number_format($tax, 2); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right; border-bottom:none;">Shipping:</td>
                <td style="border-bottom:none;">₹<?php echo number_format($shipping, 2); ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total:</td>
                <td>₹<?php echo number_format($grand_total, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <button class="btn-print" onclick="window.print()">Print Invoice</button>
    <a href="index.php" class="home-link">Back to Home</a>

</div>

</body>
</html>
