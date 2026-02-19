<?php
require_once 'db_config.php';
session_start();
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;

// Helper to calculate delivery date (7 days from order)
function getDeliveryDate($createdAt) {
    return date('d M Y', strtotime($createdAt . ' + 7 days'));
}

// Handle Mobile Number Submission
$mobile = $_GET['mobile'] ?? ($_POST['mobile'] ?? '');
$orders = [];
$error = '';

if ($mobile) {
    // Sanitize mobile
    $mobile = preg_replace('/[^\d]/', '', $mobile); // Keep only digits
    
    if (strlen($mobile) < 10) {
        $error = "Please enter a valid 10-digit mobile number.";
    } else {
        if ($mysqli) {
            // Check if status column exists (in case it wasn't added yet, though cancel_order does it)
            // Ideally we assume it exists or handle the error.
            
            // Query orders by shipping_mobile
            // We use wildcard match or exact match. Usually exact match for tracking.
            // But let's assume exact match for now to be safe.
            $stmt = $mysqli->prepare("SELECT * FROM orders WHERE shipping_mobile = ? ORDER BY created_at DESC");
            if ($stmt) {
                $stmt->bind_param("s", $mobile);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $orders[] = $row;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Nutra-Leaf Wellness</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background: #0d3625;
            color: #fff;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            transition: color 0.2s;
        }
        .navbar a:hover {
            color: #f3dc12ff;
        }
        .navbar .logo img {
            height: 50px; 
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
            flex: 1;
        }
        .search-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            margin-bottom: 30px;
        }
        .search-box input {
            padding: 12px;
            width: 300px;
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            margin-right: 10px;
        }
        .search-box button {
            padding: 12px 25px;
            background: #0d3625;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-box button:hover {
            background: #1a5c40;
        }
        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
            border-left: 5px solid #0d3625;
        }
        .order-header {
            background: #f9fbfb;
            padding: 15px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .order-id {
            font-weight: bold;
            color: #0d3625;
            font-size: 1.1em;
        }
        .order-date {
            color: #666;
            font-size: 0.9em;
        }
        .order-body {
            padding: 20px 25px;
        }
        .order-items {
            margin-bottom: 20px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 8px;
        }
        .item-name {
            color: #333;
            font-weight: 500;
        }
        .item-qty {
            color: #777;
            font-size: 0.9em;
        }
        .order-footer {
            background: #f9fbfb;
            padding: 15px 25px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .delivery-info {
            color: #0d3625;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed { background: #e6fffa; color: #0d3625; border: 1px solid #b2f5ea; }
        .status-canceled { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
        .status-delivered { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
        
        .btn-cancel {
            background: #fff;
            color: #e53e3e;
            border: 1px solid #e53e3e;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: #e53e3e;
            color: white;
        }
        .total-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #0d3625;
        }
        .no-orders {
            text-align: center;
            color: #666;
            margin-top: 50px;
            font-size: 1.1em;
        }
        
        @media (max-width: 600px) {
            .order-header, .order-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .order-footer {
                align-items: stretch;
            }
            .btn-cancel {
                text-align: center;
            }
            .search-box input {
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e0e0e0;
            z-index: 2;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -19px;
            top: 17px;
            width: 2px;
            height: 100%;
            background: #e0e0e0;
            z-index: 1;
        }
        .timeline-item:last-child::after {
            display: none;
        }
        
        .timeline-item.active::before {
            background: #28a745; /* Green */
            box-shadow: 0 0 0 4px #d4edda;
        }
        .timeline-item.active::after {
            background: #28a745;
        }
        .timeline-item.canceled::before {
            background: #dc3545; /* Red */
        }

        .timeline-title {
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }
        .timeline-date {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
        }
        .timeline-item.active .timeline-title {
            color: #0d3625;
        }
        .timeline-item.active .timeline-date {
            color: #28a745;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo"><a href="index.php"><img src="logo.jpeg" alt="logo"></a></div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="admin_dashboard.php">Admin</a>
            <?php if($userName): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="search-box">
            <h2>Track Your Order</h2>
            <p style="color:#666; margin-bottom:20px;">Enter your registered mobile number to check order status and delivery details.</p>
            <form method="GET" action="">
                <input type="text" name="mobile" placeholder="Enter Mobile Number" value="<?php echo htmlspecialchars($mobile); ?>" required pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number"><br>
                <a href="index.php" style="color:#0d3625;">Back to Shop</a><br>
                <button type="submit">Check Status</button>
            </form>
            <?php if($error): ?>
                <p style="color:red; margin-top:10px;"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>

        <?php if($mobile && empty($orders) && !$error): ?>
            <div class="no-orders">
                <p>No orders found for mobile number <strong><?php echo htmlspecialchars($mobile); ?></strong>.</p>
                <a href="index.php" style="color:#0d3625; font-weight:bold;">Start Shopping</a>
            </div>
        <?php elseif(!empty($orders)): ?>
            <h3 style="margin-bottom:20px; color:#0d3625;">Your Orders</h3>
            <?php 
                // Helper to format date or show expected
                if (!function_exists('formatDate')) {
                    function formatDate($dateStr, $default = '') {
                        return $dateStr ? date('D, d M', strtotime($dateStr)) : $default;
                    }
                }

                foreach($orders as $order): 
            ?>
                <?php 
                    $cart = json_decode($order['cart'], true) ?? [];
                    $status = $order['status'] ?? 'Confirmed';
                    $statusClass = 'status-' . strtolower($status);
                    $canCancel = ($status !== 'Canceled' && $status !== 'Delivered' && $status !== 'Shipped');
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                            <div class="order-date">Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></div>
                        </div>
                        <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                    </div>
                    
                    <div class="order-body">
                        <div class="order-items">
                            <?php foreach($cart as $item): ?>
                                <div class="item-row" style="display:flex; justify-content:space-between; align-items:center; padding:15px 0; border-bottom:1px solid #f0f0f0;">
                                    <div style="flex:1;">
                                        <div class="item-name" style="font-size:1.1em; margin-bottom:5px; color:#333;">
                                            <?php echo htmlspecialchars($item['name']); ?> 
                                            <span style="color:#888; font-size:0.9em; margin-left:5px;">x<?php echo $item['qty']; ?></span>
                                        </div>
                                        <div class="item-price" style="font-weight:bold; color:#333;">
                                            ₹<?php echo number_format($item['price'] * $item['qty'], 2); ?>
                                        </div>
                                    </div>
                                    <?php if(!empty($item['img'])): ?>
                                    <div style="margin-left:15px;">
                                        <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width:70px; height:70px; object-fit:cover; border-radius:8px; border:1px solid #eee;">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Timeline Section -->
                        <div class="timeline">
                            <?php
                                $isCanceled = ($status === 'Canceled');
                                $steps = [
                                    'Ordered' => ['label' => 'Ordered', 'date' => $order['created_at'], 'active' => true],
                                    'Packed' => ['label' => 'Packed', 'date' => $order['packed_at'] ?? null, 'active' => in_array($status, ['Packed', 'Shipped', 'Delivered'])],
                                    'Shipped' => ['label' => 'Shipped', 'date' => $order['shipped_at'] ?? null, 'active' => in_array($status, ['Shipped', 'Delivered'])],
                                    'Delivered' => ['label' => 'Delivered', 'date' => $order['delivered_at'] ?? null, 'active' => ($status === 'Delivered')]
                                ];
                                
                                // Calculate Expected Dates if not set
                                $baseTime = strtotime($order['created_at']);
                                if (!$steps['Packed']['date']) $steps['Packed']['expected'] = date('D, d M', strtotime('+1 day', $baseTime));
                                if (!$steps['Shipped']['date']) $steps['Shipped']['expected'] = date('D, d M', strtotime('+2 days', $baseTime));
                                if (!$steps['Delivered']['date']) $steps['Delivered']['expected'] = date('D, d M', strtotime('+7 days', $baseTime));
                                
                                foreach ($steps as $key => $step):
                                    $isActive = $step['active'];
                                    // If canceled, we might want to show Ordered as active but red if it was the last step?
                                    // Or just show Ordered as Green and nothing else.
                                    // If Canceled, let's just show Ordered.
                                    if ($isCanceled && $key !== 'Ordered') $isActive = false;
                                    
                                    $class = $isActive ? 'active' : '';
                                    if ($isCanceled && $key === 'Ordered') $class .= ''; // Ordered is still green/active
                                    
                                    // Date display
                                    $dateDisplay = '';
                                    if ($isActive) {
                                        $dateDisplay = formatDate($step['date']);
                                    } else if (!$isCanceled) {
                                        $dateDisplay = "Expected by " . ($step['expected'] ?? '');
                                    }
                            ?>
                            <div class="timeline-item <?php echo $class; ?>">
                                <div class="timeline-title"><?php echo $step['label']; ?></div>
                                <div class="timeline-date"><?php echo $dateDisplay; ?></div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($isCanceled): ?>
                            <div class="timeline-item canceled active">
                                <div class="timeline-title" style="color:#dc3545">Canceled</div>
                                <div class="timeline-date" style="color:#dc3545"><?php echo formatDate($order['created_at']); // Or canceled_at if we had it ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div style="text-align:right; border-top:1px dashed #eee; padding-top:10px;">
                            <span style="color:#666;">Total Amount:</span>
                            <span class="total-price"><?php echo htmlspecialchars($order['total']); ?></span>
                        </div>
                        
                        <div style="text-align:right; margin-top:10px;">
                            <a href="view_bill.php?id=<?php echo htmlspecialchars($order['order_id']); ?>" target="_blank" style="color:#0d3625; text-decoration:underline; font-weight:500;">Download Bill</a>
                        </div>
                    </div>
                    
                    <div class="order-footer">
                        <div class="delivery-info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            <span>Expected Delivery: <?php echo getDeliveryDate($order['created_at']); ?></span>
                        </div>
                        
                        <?php if($canCancel): ?>
                            <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                <input type="hidden" name="redirect_to" value="my_orders">
                                <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>">
                                <button type="submit" class="btn-cancel">Cancel Order</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>