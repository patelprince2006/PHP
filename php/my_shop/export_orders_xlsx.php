<?php
// export_orders_xlsx.php
// Requires phpoffice/phpspreadsheet via Composer.
// If vendor/autoload.php is missing, the script returns instructions to install it.

session_start();
// Check authentication: User ID 1 OR Admin Logged In
$uid = $_SESSION['user_id'] ?? 0;
$isAdmin = ($_SESSION['admin_logged_in'] ?? false) === true;

if ($uid != 1 && !$isAdmin) {
    http_response_code(403);
    echo "Access denied";
    exit;
}

$vendor = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!file_exists($vendor)) {
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "PhpSpreadsheet is not installed.\n";
    echo "Run the following in your project folder (requires Composer):\n\n";
    echo "composer require phpoffice/phpspreadsheet\n\n";
    echo "Then retry this URL.\n";
    exit;
}

require $vendor;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$ordersFile = __DIR__ . DIRECTORY_SEPARATOR . 'orders.txt';
if (!file_exists($ordersFile)) {
    http_response_code(404);
    echo "orders.txt not found";
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header row
$headers = ['order_id','user_id','user_name','cart','payment_method','upi','total','created_at'];
$sheet->fromArray($headers, NULL, 'A1');

$rowNum = 2;
$file = new SplFileObject($ordersFile);
$file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
foreach ($file as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $data = json_decode($line, true);
    if (!$data) continue;

    $cart = $data['cart'] ?? '';
    if (is_array($cart)) {
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

    $sheet->fromArray($row, NULL, 'A' . $rowNum);
    $rowNum++;
}

// Auto-size columns
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output as XLSX
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="orders.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
