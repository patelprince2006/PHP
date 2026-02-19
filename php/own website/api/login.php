<?php
// login.php — user login (JSON POST). Starts a session on success.
header('Content-Type: application/json');
require_once __DIR__ . '/conn.php';
session_start();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) { http_response_code(400); echo json_encode(['error' => 'invalid_json']); exit; }

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
if (!$email || !$password) { http_response_code(400); echo json_encode(['error' => 'missing_fields']); exit; }

$db = get_db();
if (!$db) { http_response_code(500); echo json_encode(['error' => 'db_unavailable']); exit; }

$stmt = $db->prepare('SELECT id, name, email, phone, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if (!$user) { echo json_encode(['error' => 'invalid_credentials']); exit; }

if (!password_verify($password, $user['password_hash'])) { echo json_encode(['error' => 'invalid_credentials']); exit; }

// successful login
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];

unset($user['password_hash']);
echo json_encode(['status' => 'ok', 'user' => $user]);
exit;
