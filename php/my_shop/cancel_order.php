<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    
    if (!$order_id) {
        die("Invalid Order ID");
    }

    if ($mysqli) {
        // 1. Ensure 'status' column exists
        // We can try to select it; if it fails, we add it. 
        // Or simpler: just try to add it and ignore error, or check information_schema.
        
        $checkCol = $mysqli->query("SHOW COLUMNS FROM `orders` LIKE 'status'");
        if ($checkCol && $checkCol->num_rows === 0) {
            $mysqli->query("ALTER TABLE `orders` ADD COLUMN `status` VARCHAR(50) DEFAULT 'Confirmed'");
        }

        // 2. Update the order status
        $stmt = $mysqli->prepare("UPDATE orders SET status = 'Canceled' WHERE order_id = ?");
        if ($stmt) {
            $stmt->bind_param("s", $order_id);
            if ($stmt->execute()) {
                // Success
                $redirect_to = $_POST['redirect_to'] ?? '';
                if ($redirect_to === 'my_orders') {
                    // Preserves the mobile number if possible, but for security/simplicity just go to my_orders.php
                    // Ideally we'd keep them logged in or pass the mobile back, but let's just go to the page.
                    // Actually, if we redirect to my_orders.php without params, they have to enter mobile again.
                    // Better to redirect with a success message or handle session.
                    // For now, let's just redirect to order_confirmation which shows the details and canceled status.
                    // The user can navigate back to My Orders if they want.
                    // User Request: "before delivery user can be cancel order if user can don't buy"
                    
                    // Let's actually support staying on my_orders if we can.
                    // If we passed the mobile number, we could redirect back with it.
                    if (!empty($_POST['mobile'])) {
                         header("Location: my_orders.php?mobile=" . urlencode($_POST['mobile']) . "&canceled=1");
                    } else {
                         header("Location: my_orders.php?canceled=1");
                    }
                } else {
                    header("Location: order_confirmation.php?id=" . urlencode($order_id) . "&canceled=1");
                }
                exit;
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $mysqli->error;
        }
    } else {
        die("Database connection failed");
    }
} else {
    // If accessed directly without POST, redirect home
    header("Location: index.php");
    exit;
}
