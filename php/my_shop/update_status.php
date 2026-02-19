<?php
session_start();
require_once 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';

    if ($order_id && $new_status && $mysqli) {
        // Ensure columns exist (Schema Migration on the fly)
        $columns = ['packed_at', 'shipped_at', 'delivered_at'];
        foreach ($columns as $col) {
            $check = $mysqli->query("SHOW COLUMNS FROM `orders` LIKE '$col'");
            if ($check && $check->num_rows === 0) {
                $mysqli->query("ALTER TABLE `orders` ADD COLUMN `$col` DATETIME DEFAULT NULL");
            }
        }

        // Prepare Update Query
        $sql = "UPDATE orders SET status = ?";
        $params = [$new_status];
        $types = "s";

        // Update timestamps based on status
        if ($new_status === 'Packed') {
            $sql .= ", packed_at = NOW()";
        } elseif ($new_status === 'Shipped') {
            $sql .= ", shipped_at = NOW()";
            // If packed_at is missing, set it too? Maybe not, keep it simple.
        } elseif ($new_status === 'Delivered') {
            $sql .= ", delivered_at = NOW()";
        } elseif ($new_status === 'Canceled') {
             // Maybe clear future dates? No, keep history.
        }

        $sql .= " WHERE order_id = ?"; // Use order_id (string) not id (int)
        $params[] = $order_id;
        $types .= "s";

        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect back to dashboard
header('Location: admin_dashboard.php');
exit;
