<?php
// api/admin.php — Admin-only data and actions
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
session_start_safe();
$user = require_admin();

$action = get_input('action');

// ── DASHBOARD STATS ──────────────────────────────────────────
if ($action === 'stats') {
    $stats = [
        'total_farmers'       => db_row('SELECT COUNT(*) AS c FROM users WHERE role="farmer"')['c'] ?? 0,
        'active_farmers'      => db_row('SELECT COUNT(*) AS c FROM users WHERE role="farmer" AND status="active"')['c'] ?? 0,
        'premium_subscribers' => db_row('SELECT COUNT(*) AS c FROM users WHERE subscription="Premium"')['c'] ?? 0,
        'total_storage_booked'=> db_row('SELECT COALESCE(SUM(tonnes),0) AS c FROM storage_bookings WHERE status IN ("confirmed","active")')['c'] ?? 0,
        'pending_loans'       => db_row('SELECT COUNT(*) AS c FROM loan_applications WHERE status="pending"')['c'] ?? 0,
        'monthly_revenue'     => db_row('SELECT COALESCE(SUM(amount),0) AS c FROM transactions WHERE status="completed" AND MONTH(transaction_at)=MONTH(NOW()) AND YEAR(transaction_at)=YEAR(NOW())')['c'] ?? 0,
        'system_uptime'       => 99.7,
    ];
    json_response($stats);
}

// ── FARMERS LIST ─────────────────────────────────────────────
if ($action === 'farmers') {
    $page   = max(1, get_int('page', 1));
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $search = get_input('search');
    $params = [];
    $where  = "WHERE role = 'farmer'";
    if ($search) { $where .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)'; $params = ["%$search%","%$search%","%$search%"]; }
    $total  = db_row("SELECT COUNT(*) AS c FROM users $where", $params)['c'] ?? 0;
    $rows   = db_rows("SELECT id,name,email,phone,region,subscription,status,created_at,last_login FROM users $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset", $params);
    json_response(['farmers'=>$rows,'total'=>$total,'page'=>$page,'pages'=>ceil($total/$limit)]);
}

// ── TRANSACTIONS ─────────────────────────────────────────────
if ($action === 'transactions') {
    $rows = db_rows(
        'SELECT t.*, u.name AS farmer_name FROM transactions t JOIN users u ON u.id = t.user_id
         ORDER BY t.transaction_at DESC LIMIT 50'
    );
    json_response($rows);
}

// ── LOAN MANAGEMENT ──────────────────────────────────────────
if ($action === 'loans') {
    $status = get_input('status');
    $params = [];
    $where  = '';
    if ($status) { $where = 'WHERE la.status = ?'; $params[] = $status; }
    $rows = db_rows(
        "SELECT la.*, u.name AS farmer_name, u.phone AS farmer_phone, lp.name AS product_name, lp.emoji
         FROM loan_applications la
         JOIN users u ON u.id = la.user_id
         JOIN loan_products lp ON lp.id = la.product_id
         $where ORDER BY la.applied_at DESC LIMIT 100",
        $params
    );
    json_response($rows);
}

if ($action === 'update_loan' && is_post()) {
    if (!csrf_verify()) { json_response(['ok'=>false,'error'=>'Ombi si salama.'], 403); }
    $id     = get_int('id');
    $status = get_input('status');
    $note   = get_input('note');
    $allowed = ['reviewing','approved','rejected','disbursed','closed'];
    if (!in_array($status, $allowed, true)) { json_response(['ok'=>false,'error'=>'Hali si sahihi.']); }
    db_run('UPDATE loan_applications SET status=?, note=?, approved_by=?, updated_at=NOW() WHERE id=?', [$status, $note, $user['id'], $id]);
    audit_log($user['id'], 'loan_status_update', 'loan_applications', (string)$id, ['status'=>$status]);
    json_response(['ok'=>true]);
}

// ── USER MANAGEMENT ──────────────────────────────────────────
if ($action === 'update_user' && is_post()) {
    if (!csrf_verify()) { json_response(['ok'=>false,'error'=>'Ombi si salama.'], 403); }
    $id     = get_int('id');
    $status = get_input('status');
    if (!in_array($status, ['active','inactive'], true)) { json_response(['ok'=>false,'error'=>'Hali si sahihi.']); }
    db_run('UPDATE users SET status = ? WHERE id = ? AND role = "farmer"', [$status, $id]);
    audit_log($user['id'], 'user_status_update', 'users', (string)$id, ['status'=>$status]);
    json_response(['ok'=>true]);
}

// ── PRICE UPDATE ─────────────────────────────────────────────
if ($action === 'update_price' && is_post()) {
    if (!csrf_verify()) { json_response(['ok'=>false,'error'=>'Ombi si salama.'], 403); }
    $crop_id   = get_input('crop_id');
    $region_id = get_input('region_id');
    $price     = (float) get_input('price');
    $change    = (float) get_input('change_pct');
    $trend     = get_input('trend');
    if (!$crop_id || !$region_id || $price <= 0) { json_response(['ok'=>false,'error'=>'Taarifa si sahihi.']); }
    if (!in_array($trend, ['up','down','flat'], true)) $trend = 'flat';
    db_run(
        'INSERT INTO market_prices (crop_id, region_id, price, change_pct, trend) VALUES (?, ?, ?, ?, ?)',
        [$crop_id, $region_id, $price, $change, $trend]
    );
    audit_log($user['id'], 'price_update', 'market_prices', '', ['crop'=>$crop_id,'region'=>$region_id,'price'=>$price]);
    json_response(['ok'=>true]);
}

json_response(['ok'=>false,'error'=>'Hatua haijulikani.'], 400);
