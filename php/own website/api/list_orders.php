<?php
header('Content-Type: application/json');
$cfg = require __DIR__ . '/db_config.php';
$mysqli = @new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['db']);
if($mysqli->connect_errno){
  $store = __DIR__ . '/orders.json';
  $orders = file_exists($store) ? json_decode(file_get_contents($store), true) : [];
  echo json_encode($orders);
  exit;
}

$res = $mysqli->query('SELECT order_id as id, date, name, phone, city, address, items_json, total, status FROM orders ORDER BY id DESC');
$out = [];
while($row = $res->fetch_assoc()){
  $row['items'] = json_decode($row['items_json'], true);
  unset($row['items_json']);
  $out[] = $row;
}
echo json_encode($out);
