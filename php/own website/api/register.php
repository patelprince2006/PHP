<?php
// register.php — register a new user (JSON POST)
header('Content-Type: application/json');
require_once __DIR__ . '/conn.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) { http_response_code(400); echo json_encode(['error' => 'invalid_json']); exit; }

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) { http_response_code(400); echo json_encode(['error' => 'missing_fields']); exit; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo json_encode(['error' => 'invalid_email']); exit; }

$db = get_db();
if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'db_unavailable']);
    exit;
}

// check existing
$stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) { echo json_encode(['error' => 'email_exists']); exit; }
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare('INSERT INTO users (name,email,phone,password_hash,created_at) VALUES (?,?,?,?,NOW())');
$stmt->bind_param('ssss', $name, $email, $phone, $hash);
$ok = $stmt->execute();
if (!$ok) { http_response_code(500); echo json_encode(['error' => 'insert_failed','details'=>$stmt->error]); exit; }

echo json_encode(['status' => 'ok', 'user_id' => $db->insert_id]);
exit;
