<?php
// admin_login.php — simple admin login that checks configured password and sets admin session
header('Content-Type: application/json');
require_once __DIR__ . '/db_config.php';
session_start();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) { http_response_code(400); echo json_encode(['error' => 'invalid_json']); exit; }

$password = $data['password'] ?? '';
if (!$password) { http_response_code(400); echo json_encode(['error' => 'missing_password']); exit; }

$cfg = require __DIR__ . '/db_config.php';
$expected = $cfg['admin_password'] ?? '';

// In production, use a hashed password and secure transport (HTTPS).
if (!hash_equals((string)$expected, (string)$password)) { echo json_encode(['error'=>'invalid_credentials']); exit; }

session_regenerate_id(true);
$_SESSION['is_admin'] = true;
$_SESSION['admin_logged_at'] = time();

echo json_encode(['status'=>'ok','message'=>'admin_authenticated']);
exit;
