<?php
// ============================================================
// includes/helpers.php — Shared Utility Functions
// ============================================================

function format_tzs(float $amount): string {
    return 'TZS ' . number_format($amount, 0, '.', ',');
}

function trend_arrow(string $trend): string {
    return match($trend) { 'up' => '↑', 'down' => '↓', default => '→' };
}

function trend_class(string $trend): string {
    return match($trend) { 'up' => 'cell-up', 'down' => 'cell-down', default => 'cell-flat' };
}

function hub_occupancy_pct(int $occupied, int $capacity): int {
    if ($capacity === 0) return 0;
    return min(100, (int) round($occupied / $capacity * 100));
}

function progress_fill_class(float $pct): string {
    return 'progress-fill-' . min(100, (int)(ceil($pct / 10) * 10));
}

function badge_class(string $status): string {
    return match($status) {
        'active','completed','approved','disbursed' => 'badge-green',
        'full','pending','reviewing'                => 'badge-gold',
        'inactive','failed','rejected'              => 'badge-red',
        'maintenance','cancelled'                   => 'badge-muted',
        default                                     => 'badge-sky',
    };
}

function esc(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(array $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function is_ajax(): bool { return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'; }

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): bool {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

function human_date(string $date): string {
    return date('j M Y', strtotime($date));
}

function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'Sasa hivi';
    if ($diff < 3600)   return (int)($diff/60)   . ' dak. iliyopita';
    if ($diff < 86400)  return (int)($diff/3600)  . ' saa iliyopita';
    if ($diff < 604800) return (int)($diff/86400) . ' siku iliyopita';
    return human_date($datetime);
}

function get_input(string $key, string $default = ''): string {
    return trim($_POST[$key] ?? $_GET[$key] ?? $default);
}

function get_int(string $key, int $default = 0): int {
    return (int) get_input($key, (string)$default);
}
