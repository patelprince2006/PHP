<?php
// api/list_orders.php
require 'db_connect.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$sql = "SELECT id, date, name, phone, total, status, city, pincode, items FROM orders ORDER BY date DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convert the JSON string back to a PHP array/object for the client (optional, but good practice)
        $row['items'] = json_decode($row['items'], true);
        // Format the total as a float/number
        $row['total'] = (float)$row['total'];
        $orders[] = $row;
    }
}

echo json_encode($orders);

$conn->close();
?>