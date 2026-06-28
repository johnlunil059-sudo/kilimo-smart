<?php
// dashboard.php — Farmer Dashboard
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

// Load data for this user's region
$region_id = strtolower(str_replace(' ', '-', $user['region'] ?? 'dar'));
// Map region name to ID
$region_map = ['dar es salaam'=>'dar','morogoro'=>'morogoro','iringa'=>'iringa','dodoma'=>'dodoma','arusha'=>'arusha','mbeya'=>'mbeya','mwanza'=>'mwanza','tanga'=>'tanga'];
$rid = $region_map[strtolower($user['region'] ?? '')] ?? 'dar';

// Top prices for user's region
$prices = db_rows(
    'SELECT mp.*, c.name AS crop_name, c.emoji, c.color
     FROM market_prices mp
     JOIN crops c ON c.id = mp.crop_id
     WHERE mp.region_id = ?
       AND mp.recorded_at = (SELECT MAX(mp2.recorded_at) FROM market_prices mp2 WHERE mp2.crop_id=mp.crop_id AND mp2.region_id=mp.region_id)
     ORDER BY mp.price DESC LIMIT 5',
    [$rid]
);

// My active bookings
$bookings = db_rows(
    'SELECT sb.*, sh.name AS hub_name, c.name AS crop_name, c.emoji FROM storage_bookings sb
     JOIN storage_hubs sh ON sh.id = sb.hub_id JOIN crops c ON c.id = sb.crop_id
     WHERE sb.user_id = ? AND sb.status IN ("confirmed","active") ORDER BY sb.created_at DESC LIMIT 3',
    [$user['id']]
);

// My loan applications
$loans = db_rows(
    'SELECT la.*, lp.name AS product_name, lp.emoji FROM loan_applications la
     JOIN loan_products lp ON lp.id = la.product_id
     WHERE la.user_id = ? ORDER BY la.applied_at DESC LIMIT 3',
    [$user['id']]
);

// Notifications (unread)
$notifs = db_rows('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5', [$user['id']]);

// Advisory tips
$tips = db_rows('SELECT at.*, u.name AS author_name FROM advisory_tips at LEFT JOIN users u ON u.id = at.author_id WHERE at.published=1 ORDER BY at.created_at DESC LIMIT 3');

// Hubs overview
$hubs = db_rows('SELECT * FROM storage_hubs ORDER BY status ASC LIMIT 4');

// Weather alert for region
$alert = db_row('SELECT * FROM weather_alerts WHERE region_id = ? AND active = 1 LIMIT 1', [$rid]);

render_head('Dashibodi');
?>
<style>
.mini-chart{display:flex;align-items:flex-end;gap:4px;height:44px;padding:4px 0;}
.mini-bar{flex:1;border-radius:3px 3px 0 0;transition:height .5s ease,opacity var(--tr-base);cursor:default;}
.mini-bar:hover{opacity:.8;}
.mini-bar-up{background:var(--clr-green-400);}
.mini-bar-down{background:var(--clr-red-400);}
.mini-bar-flat{background:var(--clr-text-muted);}
.price-ticker-wrap{overflow:hidden;background:var(--clr-bg-tertiary);border-bottom:var(--glass-border);border-top:var(--glass-border);}
.price-ticker{display:flex;gap:var(--space-12);padding:var(--space-3) 0;animation:tickerScroll 35s linear infinite;white-space:nowrap;width:max-content;}
@keyframes tickerScroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.price-ticker:hover{animation-play-state:paused;}
.ticker-item{display:inline-flex;align-items:center;gap:var(--space-2);font-size:.82rem;font-weight:600;flex-shrink:0;}
.activity-item{display:flex;align-items:flex-start;gap:var(--space-3);padding:var(--space-3) 0;border-bottom:1px solid rgba(82,181,100,.07);}
.activity-item:last-child{border-bottom:none;}
.activity-dot{width:34px;height:34px;border-radius:var(--radius-full);display:grid;place-items:center;font-size:.9rem;flex-shrink:0;margin-top:2px;}
.activity-dot.green{background:rgba(58,143,74,.22);}
.activity-dot.sky{background:rgba(52,152,219,.22);}
.activity-dot.gold{background:rgba(232,184,48,.18);}
.hub-row{display:flex;align-items:center;gap:var(--space-4);padding:var(--space-3) 0;border-bottom:1px solid rgba(82,181,100,.07);}
.hub-row:last-child{border-bottom:none;}
.hub-info{flex:1;}
.hub-name{font-size:.88rem;font-weight:600;}
.hub-region{font-size:.75rem;color:var(--clr-text-muted);}
</style>

<?php render_layout_open(); render_sidebar($user, 'dashboard'); ?>
<div class="content-wrap">
<?php render_topbar($user, 'Dashibodi'); ?>

<!-- TICKER -->
<div class="price-ticker-wrap">
  <div class="price-ticker">
    <?php $ticker_prices = db_rows(
      'SELECT mp.*, c.name AS crop_name, c.emoji FROM market_prices mp JOIN crops c ON c.id=mp.crop_id
       WHERE mp.recorded_at=(SELECT MAX(m2.recorded_at) FROM market_prices m2 WHERE m2.crop_id=mp.crop_id AND m2.region_id=mp.region_id) AND mp.region_id=?',
      [$rid]
    );
    $ticker_data = array_merge($ticker_prices, $ticker_prices); // duplicate for seamless loop
    foreach ($ticker_data as $t): ?>
    <div class="ticker-item">
      <span><?= $t['emoji'] ?></span>
      <span><?= esc($t['crop_name']) ?></span>
      <span style="color:var(--clr-text-accent);font-family:var(--font-display)">TZS <?= number_format($t['price']) ?></span>
      <span class="<?= trend_class($t['trend']) ?>"><?= trend_arrow($t['trend']) ?><?= abs($t['change_pct']) ?>%</span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="page-content">
  <?php if ($alert): ?>
  <div class="alert alert-<?= esc($alert['type']) ?> anim-fade-in-up" style="margin-bottom:var(--space-6)">
    ⚠️ <?= esc($alert['message']) ?>
  </div>
  <?php endif; ?>

  <!-- KPI CARDS -->
  <div class="grid-4 mb-8">
    <?php
    $region_prices = $prices;
    $avg_change = count($region_prices) ? round(array_sum(array_column($region_prices,'change_pct'))/count($region_prices),1) : 0;
    $total_storage = array_sum(array_column($bookings,'tonnes'));
    $active_loans  = count(array_filter($loans, fn($l)=>in_array($l['status'],['approved','disbursed'])));
    $kpis = [
      ['label'=>'Bei za Leo ('.$user['region'].')','value'=>count($prices).' Mazao','sub'=>$avg_change>=0?"+{$avg_change}% wastani":"{$avg_change}% wastani",'icon'=>'📊','color'=>'green'],
      ['label'=>'Hifadhi Yangu','value'=>$total_storage.' t','sub'=>count($bookings).' Mikataba','icon'=>'🏪','color'=>'gold'],
      ['label'=>'Mikopo Yangu','value'=>count($loans).' Maombi','sub'=>$active_loans.' Inayoendelea','icon'=>'💳','color'=>'sky'],
      ['label'=>'Arifa Mpya','value'=>unread_notifications_count($user['id']),'sub'=>'Habari za leo','icon'=>'🔔','color'=>'orange'],
    ];
    foreach ($kpis as $k): ?>
    <div class="stat-card anim-fade-in-up">
      <div class="stat-icon <?= $k['color'] ?>"><?= $k['icon'] ?></div>
      <div class="stat-value"><?= esc($k['value']) ?></div>
      <div class="stat-label"><?= esc($k['label']) ?></div>
      <div class="stat-sub"><?= esc($k['sub']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-6);">

    <!-- TOP PRICES -->
    <div class="card anim-fade-in-up">
      <div class="card-header">
        <div class="card-title">📊 Bei za <?= esc($user['region']) ?></div>
        <a href="market.php" class="btn btn-sm btn-outline">Zote</a>
      </div>
      <?php foreach ($prices as $p): ?>
      <div class="hub-row">
        <div style="font-size:1.4rem"><?= $p['emoji'] ?></div>
        <div class="hub-info">
          <div class="hub-name"><?= esc($p['crop_name']) ?></div>
          <div class="hub-region"><?= esc($p['region_name'] ?? $user['region']) ?></div>
        </div>
        <div style="text-align:right">
          <div style="font-family:var(--font-display);font-weight:700;color:var(--clr-text-accent)">TZS <?= number_format($p['price']) ?></div>
          <div class="<?= trend_class($p['trend']) ?>" style="font-size:.75rem"><?= trend_arrow($p['trend']) ?><?= $p['change_pct'] ?>%</div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$prices): ?><p style="color:var(--clr-text-muted);font-size:.85rem">Hakuna bei kwa mkoa wako leo.</p><?php endif; ?>
    </div>

    <!-- STORAGE HUBS -->
    <div class="card anim-fade-in-up">
      <div class="card-header">
        <div class="card-title">🏪 Hali ya Maghala</div>
        <a href="storage.php" class="btn btn-sm btn-outline">Hifadhi</a>
      </div>
      <?php foreach ($hubs as $h):
        $pct = hub_occupancy_pct((int)$h['occupied'], (int)$h['capacity']); ?>
      <div class="hub-row">
        <div class="hub-info">
          <div class="hub-name"><?= esc($h['name']) ?></div>
          <div class="hub-region"><?= esc($h['region_id']) ?> · <?= $pct ?>% imejaa</div>
          <div class="progress-bar-wrap" style="margin-top:4px;height:4px"><div class="progress-bar-fill <?= progress_fill_class($pct) ?>"></div></div>
        </div>
        <span class="badge <?= $h['status']==='full'?'badge-red':($h['status']==='active'?'badge-green':'badge-muted') ?>"><?= esc($h['status']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ADVISORY TIPS -->
    <div class="card anim-fade-in-up">
      <div class="card-header">
        <div class="card-title">📖 Ushauri wa Kilimo</div>
      </div>
      <?php foreach ($tips as $tip): ?>
      <div class="activity-item">
        <div class="activity-dot green" style="font-size:1.2rem"><?= $tip['emoji'] ?? '🌱' ?></div>
        <div>
          <div class="activity-title"><?= esc($tip['title']) ?></div>
          <div class="activity-sub"><?= esc(mb_substr($tip['body'], 0, 90)) ?>…</div>
          <div class="activity-sub" style="margin-top:4px">— <?= esc($tip['author_name'] ?? 'Enimelda Raphael') ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div><!-- /grid -->

  <!-- MY LOANS -->
  <?php if ($loans): ?>
  <div class="card anim-fade-in-up" style="margin-top:var(--space-6)">
    <div class="card-header">
      <div class="card-title">💳 Maombi Yangu ya Mikopo</div>
      <a href="loans.php" class="btn btn-sm btn-outline">Onyesha Zote</a>
    </div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Bidhaa</th><th>Kiasi</th><th>Hali</th><th>Tarehe</th></tr></thead>
        <tbody>
          <?php foreach ($loans as $l): ?>
          <tr>
            <td><?= $l['emoji'] ?> <?= esc($l['product_name']) ?></td>
            <td><?= format_tzs($l['amount']) ?></td>
            <td><span class="badge <?= badge_class($l['status']) ?>"><?= esc($l['status']) ?></span></td>
            <td><?= human_date($l['applied_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /page-content -->
</div><!-- /content-wrap -->
<?php render_layout_close(); render_foot(); ?>
