<?php
// api/save_order.php
require 'db_connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// Get the raw JSON body
$json_data = file_get_contents('php://input');
$order = json_decode($json_data, true);

// Basic validation
if (!isset($order['id'], $order['name'], $order['phone'], $order['total'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required order data.']);
    exit;
}

// Prepare data for SQL
$id = clean_input($conn, $order['id']);
$date = date('Y-m-d H:i:s'); // Use server time
$name = clean_input($conn, $order['name']);
$email = clean_input($conn, $order['email'] ?? ''); // UserDetails interface has email
$phone = clean_input($conn, $order['phone']);
$address = clean_input($conn, $order['address']);
$city = clean_input($conn, $order['city']);
$pincode = clean_input($conn, $order['pincode']);
$total = (float)$order['total'];
$status = clean_input($conn, $order['status'] ?? 'Verified');

// Convert items array to JSON string for storage
$items_json = json_encode($order['items']);

// Insert query
$stmt = $conn->prepare("INSERT INTO orders (id, date, name, email, phone, address, city, pincode, items, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssds", $id, $date, $name, $email, $phone, $address, $city, $pincode, $items_json, $total, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order saved successfully.', 'orderId' => $id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>