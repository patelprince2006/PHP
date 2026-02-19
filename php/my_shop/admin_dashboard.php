<?php
session_start();
require_once 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Fetch Stats
$total_sales = 0;
$active_orders = 0;
$inventory_alerts = "2 Low"; // Mocked as products are in JS

if ($mysqli) {
    // Calculate Total Sales correctly by fetching all rows (since 'total' is a string with currency symbol)
    $res_sales = $mysqli->query("SELECT total FROM orders");
    if ($res_sales) {
        while ($row = $res_sales->fetch_assoc()) {
            // Remove non-numeric characters (except dot)
            $clean_val = preg_replace('/[^\d.]/', '', $row['total']);
            $total_sales += (float)$clean_val;
        }
    }

    $res_count = $mysqli->query("SELECT COUNT(*) as count FROM orders");
    if ($res_count && $row = $res_count->fetch_assoc()) {
        $active_orders = $row['count'] ?? 0;
    }
}

// Fetch Orders
$orders = [];
if ($mysqli) {
    $res_orders = $mysqli->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10"); // Limit for dashboard
    if ($res_orders) {
        while ($row = $res_orders->fetch_assoc()) {
            $orders[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #0d3625;
        }
        .btn-export {
            background-color: #0d3625;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-export:hover {
            background-color: #1a4d36;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            bottom: 15%;
            width: 4px;
            border-radius: 2px;
        }
        
        .stat-card.sales { background-color: #e8f5e9; }
        .stat-card.sales::before { background-color: #0d3625; }
        .stat-card.orders { background-color: #f3f9fc; }
        .stat-card.orders::before { background-color: #022f46; }
        .stat-card.inventory { background-color: #fff8e1; }
        .stat-card.inventory::before { background-color: #ff6f00; }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            padding-left: 10px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            padding-left: 10px;
        }
        .stat-card.sales .stat-value { color: #0d3625; } /* Dark Green */
        .stat-card.inventory .stat-value { color: #ff6f00; } /* Orange */

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 15px 20px;
            font-size: 13px;
            color: #666;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        td {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            color: #333;
            font-size: 14px;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .status {
            font-weight: bold;
            font-size: 13px;
        }
        .status.delivered { color: #0d3625; }
        .status.shipped { color: #e65100; }
        .status.pending { color: #f9a825; }
        
        .logout-link {
            margin-left: 20px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        .logout-link:hover {
            color: #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-section">
            <span class="material-icons" style="font-size: 32px;">storage</span>
            <span>Dashboard</span>
        </div>
        <div style="display:flex; align-items:center;">
             <a href="admin_logout.php" class="logout-link">Logout</a>
             <a href="export_orders.php" class="btn-export" style="margin-left: 20px;">
                <span class="material-icons">download</span>
                Export Records
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card sales">
            <div class="stat-label">TOTAL SALES</div>
            <div class="stat-value">₹<?php echo number_format($total_sales, 2); ?></div>
        </div>
        <div class="stat-card orders">
            <div class="stat-label">ACTIVE ORDERS</div>
            <div class="stat-value"><?php echo $active_orders; ?></div>
        </div>
        <div class="stat-card inventory">
            <div class="stat-label">INVENTORY ALERTS</div>
            <div class="stat-value">2 <span style="font-size: 0.5em; font-weight: normal;">Low</span></div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                            <td>
                                <?php 
                                    $user_display = $order['user_name'] ?? ('User ' . $order['user_id']);
                                    echo htmlspecialchars($user_display); 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    // Parse cart to show summary or count
                                    $cart_content = $order['cart'] ?? '';
                                    // Truncate if too long
                                    if (strlen($cart_content) > 50) {
                                        echo htmlspecialchars(substr($cart_content, 0, 50)) . '...';
                                    } else {
                                        echo htmlspecialchars($cart_content);
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    // Remove '₹' or other non-numeric chars before formatting
                                    $clean_total = preg_replace('/[^\d.]/', '', $order['total']);
                                    echo '₹' . number_format((float)$clean_total, 2); 
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $current_status = $order['status'] ?? 'Confirmed';
                                    $status_colors = [
                                        'Confirmed' => '#333',
                                        'Packed' => '#007bff',
                                        'Shipped' => '#ff9800',
                                        'Delivered' => '#28a745',
                                        'Canceled' => '#dc3545'
                                    ];
                                    $color = $status_colors[$current_status] ?? '#333';
                                ?>
                                <form action="update_status.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding:5px; border-radius:4px; border:1px solid <?php echo $color; ?>; color: <?php echo $color; ?>; font-weight:bold; cursor:pointer;">
                                        <option value="Confirmed" <?php echo $current_status == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Packed" <?php echo $current_status == 'Packed' ? 'selected' : ''; ?>>Packed</option>
                                        <option value="Shipped" <?php echo $current_status == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $current_status == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Canceled" <?php echo $current_status == 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
