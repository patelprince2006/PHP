<?php
// save_order.php — accepts JSON order and saves to MySQL (or file fallback)
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if(!$input){ http_response_code(400); echo json_encode(['error'=>'invalid payload']); exit; }

$cfg = require __DIR__ . '/db_config.php';
$mysqli = @new mysqli($cfg['host'],$cfg['user'],$cfg['pass'],$cfg['db']);
if($mysqli->connect_errno){
  // fallback to file storage
  $store = __DIR__ . '/orders.json';
  $orders = file_exists($store) ? json_decode(file_get_contents($store), true) : [];
  array_unshift($orders, $input);
  file_put_contents($store, json_encode($orders, JSON_PRETTY_PRINT));
  echo json_encode(['status'=>'saved_file']); exit;
}

$stmt = $mysqli->prepare('INSERT INTO orders (`order_id`,`date`,`name`,`phone`,`city`,`address`,`items_json`,`total`,`status`) VALUES (?,?,?,?,?,?,?,?,?)');
$items_json = json_encode($input['items']);
$stmt->bind_param('sssssssss',$input['id'],$input['date'],$input['name'],$input['phone'],$input['city'],$input['address'],$items_json,$input['total'],$input['status']);
$ok = $stmt->execute();
if(!$ok){ http_response_code(500); echo json_encode(['error'=>$stmt->error]); exit; }
echo json_encode(['status'=>'saved_db']);
