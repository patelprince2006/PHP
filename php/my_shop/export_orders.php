<?php
// export_orders.php
// Reads orders.txt (one JSON object per line) and outputs a CSV file Excel can open.

session_start();
// Check authentication: User ID 1 OR Admin Logged In
$uid = $_SESSION['user_id'] ?? 0;
$isAdmin = ($_SESSION['admin_logged_in'] ?? false) === true;

if ($uid != 1 && !$isAdmin) {
    http_response_code(403);
    echo "Access denied";
    exit;
}

$ordersFile = __DIR__ . DIRECTORY_SEPARATOR . 'orders.txt';
if (!file_exists($ordersFile)) {
    http_response_code(404);
    echo "orders.txt not found";
    exit;
}

// Send headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders.csv"');

// write UTF-8 BOM so Excel displays UTF-8 correctly
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
// Header row
fputcsv($out, ['order_id','user_id','user_name','cart','payment_method','upi','total','created_at']);

$file = new SplFileObject($ordersFile);
$file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
foreach ($file as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $data = json_decode($line, true);
    if (!$data) {
        // try to skip malformed lines
        continue;
    }
    // cart may be array — stringify it for CSV
    $cart = $data['cart'] ?? '';
    if (is_array($cart)) {
        // create a readable summary: name x qty @ price
        $items = [];
        foreach ($cart as $it) {
            $n = $it['name'] ?? ($it['id'] ?? 'item');
            $q = $it['qty'] ?? ($it['quantity'] ?? 1);
            $p = isset($it['price']) ? (string)$it['price'] : '';
            $items[] = $n . ' x' . $q . ($p !== '' ? ' @' . $p : '');
        }
        $cartStr = implode(' | ', $items);
    } else {
        $cartStr = (string)$cart;
    }

    $row = [
        $data['order_id'] ?? '',
        $data['user_id'] ?? $data['user'] ?? '',
        $data['user_name'] ?? '',
        $cartStr,
        $data['payment'] ?? $data['payment_method'] ?? '',
        $data['upi'] ?? '',
        $data['total'] ?? '',
        $data['created_at'] ?? ''
    ];
    fputcsv($out, $row);
}

fclose($out);
exit;
