<?php
// api/loans.php — Loan application handler
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
session_start_safe();
$user = require_auth();

if (!is_post()) { json_response(['ok'=>false,'error'=>'POST required'], 405); }
if (!csrf_verify()) { json_response(['ok'=>false,'error'=>'Ombi si salama.'], 403); }

$action = get_input('action');

if ($action === 'apply') {
    $product_id = get_input('product_id');
    $amount     = (float) get_input('amount');
    $purpose    = get_input('purpose');
    $farm_size  = get_input('farm_size');
    $crop_season= get_input('crop_season');
    $guarantor  = get_input('guarantor');

    if (!$product_id || $amount <= 0) {
        json_response(['ok'=>false,'error'=>'Tafadhali jaza taarifa zote.']);
    }
    $product = db_row('SELECT * FROM loan_products WHERE id = ? AND active = 1', [$product_id]);
    if (!$product) { json_response(['ok'=>false,'error'=>'Bidhaa ya mkopo haipatikani.']); }
    if ($amount < $product['min_amount'] || $amount > $product['max_amount']) {
        json_response(['ok'=>false,'error'=>'Kiasi nje ya mipaka inayoruhusiwa.']);
    }
    $id = db_run(
        'INSERT INTO loan_applications (user_id, product_id, amount, purpose, farm_size, crop_season, guarantor, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, "pending")',
        [$user['id'], $product_id, $amount, $purpose, $farm_size, $crop_season, $guarantor]
    );
    audit_log($user['id'], 'loan_apply', 'loan_applications', (string)$id, ['product_id'=>$product_id,'amount'=>$amount]);
    json_response(['ok'=>true,'id'=>$id,'message'=>'Ombi lako limetumwa! Tutawasiliana nawe siku 7 za kazi.']);
}

if ($action === 'my_loans') {
    $rows = db_rows(
        'SELECT la.*, lp.name AS product_name, lp.emoji, lp.interest_rate, lp.tenure_months
         FROM loan_applications la
         JOIN loan_products lp ON lp.id = la.product_id
         WHERE la.user_id = ? ORDER BY la.applied_at DESC',
        [$user['id']]
    );
    json_response(['ok'=>true,'loans'=>$rows]);
}

json_response(['ok'=>false,'error'=>'Hatua haijulikani.'], 400);
