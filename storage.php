<?php
// api/storage.php — Storage booking handler
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
session_start_safe();
$user = require_auth();

$action = get_input('action');

if ($action === 'hubs') {
    $region = get_input('region');
    $sql    = 'SELECT sh.*, r.name AS region_name FROM storage_hubs sh JOIN regions r ON r.id = sh.region_id';
    $params = [];
    if ($region) { $sql .= ' WHERE sh.region_id = ?'; $params[] = $region; }
    $sql .= ' ORDER BY sh.region_id';
    json_response(db_rows($sql, $params));
}

if ($action === 'book' && is_post()) {
    if (!csrf_verify()) { json_response(['ok'=>false,'error'=>'Ombi si salama.'], 403); }
    $hub_id  = get_input('hub_id');
    $crop_id = get_input('crop_id');
    $tonnes  = (float) get_input('tonnes');
    $months  = get_int('months', 1);
    if (!$hub_id || !$crop_id || $tonnes <= 0 || $months < 1) {
        json_response(['ok'=>false,'error'=>'Tafadhali jaza taarifa zote.']);
    }
    $hub = db_row('SELECT * FROM storage_hubs WHERE id = ?', [$hub_id]);
    if (!$hub) { json_response(['ok'=>false,'error'=>'Ghala halipatikani.']); }
    if ($hub['status'] === 'full') { json_response(['ok'=>false,'error'=>'Ghala hili limejaa. Chagua jingine.']); }
    $available = $hub['capacity'] - $hub['occupied'];
    if ($tonnes > $available) { json_response(['ok'=>false,'error'=>"Nafasi iliyobaki ni {$available} tani tu."]); }

    $total = $tonnes * $hub['price_per_tonne'] * $months;
    $start = date('Y-m-d');
    $end   = date('Y-m-d', strtotime("+{$months} month"));

    db()->beginTransaction();
    $id = db_run(
        'INSERT INTO storage_bookings (user_id, hub_id, crop_id, tonnes, months, total_cost, status, start_date, end_date)
         VALUES (?, ?, ?, ?, ?, ?, "confirmed", ?, ?)',
        [$user['id'], $hub_id, $crop_id, $tonnes, $months, $total, $start, $end]
    );
    db_run('UPDATE storage_hubs SET occupied = occupied + ? WHERE id = ?', [$tonnes, $hub_id]);
    // Record transaction
    db_run('INSERT INTO transactions (user_id, type, amount, status) VALUES (?, "Storage Fee", ?, "pending")', [$user['id'], $total]);
    db()->commit();
    audit_log($user['id'], 'storage_book', 'storage_bookings', (string)$id, ['hub_id'=>$hub_id,'tonnes'=>$tonnes]);
    json_response(['ok'=>true,'id'=>$id,'total'=>$total,'message'=>'Nafasi imehifadhiwa! Lipa kupitia simu yako.']);
}

json_response(['ok'=>false,'error'=>'Hatua haijulikani.'], 400);
