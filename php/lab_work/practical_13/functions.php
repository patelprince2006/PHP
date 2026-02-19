<?php
declare(strict_types=1);

function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize_text(string $s): string {
    $s = trim($s);
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s);
    return $s;
}

function validate_username(string $username): ?string {
    $username = sanitize_text($username);
    if ($username === '') return 'Username is required.';
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        return 'Username must be 3-30 characters and contain only letters, numbers and underscores.';
    }
    return null;
}

function validate_email(string $email): ?string {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if ($email === false || $email === '') return 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Email is not valid.';
    return null;
}

function validate_password(string $pw): ?string {
    if ($pw === '') return 'Password is required.';
    if (mb_strlen($pw) < 8) return 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Za-z]/', $pw) || !preg_match('/[0-9]/', $pw)) {
        return 'Password must contain at least one letter and one number.';
    }
    return null;
}
