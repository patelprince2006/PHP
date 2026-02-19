<?php
// export_orders.php — exports orders as CSV. Uses DB if available, else file fallback.
require_once __DIR__ . '/db_config.php';
$cfg = require __DIR__ . '/db_config.php';
$mysqli = @new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['db']);
$rows = [];
if($mysqli->connect_errno){
  $store = __DIR__ . '/orders.json';
  $rows = file_exists($store) ? json_decode(file_get_contents($store), true) : [];
} else {
  $res = $mysqli->query('SELECT order_id as id, date, name, phone, city, address, items_json, total, status FROM orders ORDER BY id DESC');
  if($res){
    while($r = $res->fetch_assoc()){
      $r['items'] = json_decode($r['items_json'], true);
      unset($r['items_json']);
      $rows[] = $r;
    }
  }
}

// Send CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders_export_' . date('Ymd_His') . '.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Order ID','Date','Customer','Phone','City','Address','Items','Total','Status']);

foreach($rows as $r){
  $itemsStr = '';
  if(!empty($r['items']) && is_array($r['items'])){
    $parts = [];
    foreach($r['items'] as $it){
      $name = isset($it['name']) ? $it['name'] : (isset($it['id']) ? $it['id'] : 'item');
      $qty = isset($it['quantity']) ? $it['quantity'] : 1;
      $parts[] = $name . ' (x' . $qty . ')';
    }
    $itemsStr = implode('; ', $parts);
  }

  fputcsv($out, [
    $r['id'] ?? '',
    $r['date'] ?? '',
    $r['name'] ?? '',
    $r['phone'] ?? '',
    $r['city'] ?? '',
    $r['address'] ?? '',
    $itemsStr,
    $r['total'] ?? '',
    $r['status'] ?? ''
  ]);
}

fclose($out);
exit;
