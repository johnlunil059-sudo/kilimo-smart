<?php
// ============================================================
// includes/auth.php — Session-based Authentication
// ============================================================
require_once __DIR__ . '/db.php';

function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function auth_login(string $email, string $password): array {
    $user = db_row(
        'SELECT * FROM users WHERE email = ? AND status = "active" LIMIT 1',
        [strtolower(trim($email))]
    );
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'error' => 'Barua pepe au nywila si sahihi.'];
    }
    // Update last login
    db_run('UPDATE users SET last_login = NOW() WHERE id = ?', [$user['id']]);
    // Log audit
    audit_log($user['id'], 'login');

    unset($user['password_hash']);
    $_SESSION['user'] = $user;
    $_SESSION['login_time'] = time();
    return ['ok' => true, 'user' => $user];
}

function auth_register(string $name, string $phone, string $email, string $region, string $password): array {
    if (!$name || !$phone || !$email || !$region || !$password) {
        return ['ok' => false, 'error' => 'Tafadhali jaza sehemu zote.'];
    }
    if (strlen($password) < 6) {
        return ['ok' => false, 'error' => 'Nywila iwe na herufi 6 au zaidi.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Barua pepe si sahihi.'];
    }
    $exists = db_row('SELECT id FROM users WHERE email = ? LIMIT 1', [strtolower(trim($email))]);
    if ($exists) {
        return ['ok' => false, 'error' => 'Barua pepe tayari ipo kwenye mfumo.'];
    }
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $id = db_run(
        'INSERT INTO users (name, email, phone, region, password_hash, role, avatar, title, subscription, status)
         VALUES (?, ?, ?, ?, ?, "farmer", "🧑‍🌾", "Mkulima", "Free", "active")',
        [trim($name), strtolower(trim($email)), trim($phone), $region, $hash]
    );
    $user = db_row('SELECT * FROM users WHERE id = ? LIMIT 1', [$id]);
    unset($user['password_hash']);
    $_SESSION['user'] = $user;
    $_SESSION['login_time'] = time();
    audit_log($id, 'register');
    return ['ok' => true, 'user' => $user];
}

function auth_logout(): void {
    session_start_safe();
    if (isset($_SESSION['user'])) {
        audit_log($_SESSION['user']['id'], 'logout');
    }
    $_SESSION = [];
    session_destroy();
    header('Location: /index.php');
    exit;
}

function get_session(): ?array {
    session_start_safe();
    if (!isset($_SESSION['user'])) return null;
    // Expire after SESSION_LIFETIME
    if (time() - ($_SESSION['login_time'] ?? 0) > SESSION_LIFETIME) {
        auth_logout();
    }
    return $_SESSION['user'];
}

function require_auth(): array {
    $user = get_session();
    if (!$user) {
        header('Location: /index.php');
        exit;
    }
    return $user;
}

function require_admin(): array {
    $user = require_auth();
    if ($user['role'] !== 'admin') {
        header('Location: dashboard.php');
        exit;
    }
    return $user;
}

function audit_log(int $user_id, string $action, string $entity = '', string $entity_id = '', array $details = []): void {
    try {
        db_run(
            'INSERT INTO audit_log (user_id, action, entity, entity_id, details, ip_address)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$user_id, $action, $entity, $entity_id, $details ? json_encode($details) : null, $_SERVER['REMOTE_ADDR'] ?? null]
        );
    } catch (Throwable) { /* never break app for a log */ }
}
