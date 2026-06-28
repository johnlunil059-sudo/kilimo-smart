<?php
// api/prices.php — Market prices JSON endpoint
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
session_start_safe();
require_auth();

header('Content-Type: application/json; charset=utf-8');

$crop   = get_input('crop');
$region = get_input('region');

$sql = 'SELECT mp.*, c.name AS crop_name, c.emoji, c.color, r.name AS region_name
        FROM market_prices mp
        JOIN crops c   ON c.id = mp.crop_id
        JOIN regions r ON r.id = mp.region_id
        WHERE mp.recorded_at = (
            SELECT MAX(mp2.recorded_at) FROM market_prices mp2
            WHERE mp2.crop_id = mp.crop_id AND mp2.region_id = mp.region_id
        )';
$params = [];
if ($crop)   { $sql .= ' AND mp.crop_id = ?';   $params[] = $crop; }
if ($region) { $sql .= ' AND mp.region_id = ?'; $params[] = $region; }
$sql .= ' ORDER BY c.name, r.name';

echo json_encode(db_rows($sql, $params), JSON_UNESCAPED_UNICODE);
