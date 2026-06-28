<?php
// market.php — Market Prices
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

$crops   = db_rows('SELECT * FROM crops ORDER BY name');
$regions = db_rows('SELECT * FROM regions ORDER BY name');

// Build full price matrix (latest price per crop/region)
$all_prices = db_rows(
    'SELECT mp.crop_id, mp.region_id, mp.price, mp.change_pct, mp.trend
     FROM market_prices mp
     WHERE mp.recorded_at = (
         SELECT MAX(mp2.recorded_at) FROM market_prices mp2
         WHERE mp2.crop_id = mp.crop_id AND mp2.region_id = mp.region_id
     )'
);
// Index: $price_matrix[crop_id][region_id] = row
$price_matrix = [];
foreach ($all_prices as $p) {
    $price_matrix[$p['crop_id']][$p['region_id']] = $p;
}

render_head('Bei za Mazao');
?>
<style>
.price-hero{background:linear-gradient(135deg,rgba(39,105,52,.3) 0%,rgba(15,41,22,.5) 100%);border:var(--glass-border);border-radius:var(--radius-xl);padding:var(--space-8);margin-bottom:var(--space-8);display:flex;align-items:center;justify-content:space-between;gap:var(--space-6);flex-wrap:wrap;}
.region-matrix{display:grid;grid-template-columns:auto repeat(<?= count($regions) ?>,1fr);border:var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;font-size:.82rem;}
.matrix-cell{padding:var(--space-3);border-right:1px solid rgba(82,181,100,.08);border-bottom:1px solid rgba(82,181,100,.08);text-align:center;white-space:nowrap;transition:background var(--tr-fast);}
.matrix-cell:hover{background:rgba(58,143,74,.12);}
.matrix-header{background:var(--clr-bg-tertiary);font-weight:700;color:var(--clr-text-muted);font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;}
.matrix-crop-header{background:var(--clr-bg-tertiary);font-weight:700;font-size:.78rem;text-align:left;padding-left:var(--space-4);}
.matrix-price{font-weight:700;font-size:.8rem;}
.cell-up{color:var(--clr-green-300);}.cell-down{color:var(--clr-red-400);}.cell-flat{color:var(--clr-text-muted);}
.crop-filter-row{display:flex;gap:var(--space-2);flex-wrap:wrap;margin-bottom:var(--space-6);}
.crop-filter-btn{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);border-radius:var(--radius-full);font-size:.8rem;font-weight:600;border:1px solid var(--clr-border);background:var(--clr-surface-1);color:var(--clr-text-muted);cursor:pointer;transition:all var(--tr-base);}
.crop-filter-btn.active,.crop-filter-btn:hover{background:var(--gradient-green);color:#fff;border-color:transparent;}
</style>
<?php render_layout_open(); render_sidebar($user, 'market'); ?>
<div class="content-wrap">
<?php render_topbar($user, 'Bei za Mazao'); ?>
<div class="page-content">

  <!-- HERO -->
  <div class="price-hero anim-fade-in-up">
    <div>
      <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:var(--space-2)">📊 Bei za Mazao — Tanzania</h1>
      <p class="section-copy">Bei zinasasishwa kila siku kutoka masoko ya mikoa yote</p>
    </div>
    <div style="display:flex;gap:var(--space-6)">
      <div style="text-align:center"><div style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--clr-green-200)"><?= count($crops) ?></div><div style="font-size:.78rem;color:var(--clr-text-muted)">Mazao</div></div>
      <div style="text-align:center"><div style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--clr-gold-400)"><?= count($regions) ?></div><div style="font-size:.78rem;color:var(--clr-text-muted)">Mikoa</div></div>
    </div>
  </div>

  <!-- CROP FILTERS -->
  <div class="crop-filter-row">
    <button class="crop-filter-btn active" onclick="filterCrop('all',this)">🌾 Mazao Yote</button>
    <?php foreach ($crops as $c): ?>
    <button class="crop-filter-btn" onclick="filterCrop('<?= esc($c['id']) ?>',this)"><?= $c['emoji'] ?> <?= esc($c['name_sw'] ?? $c['name']) ?></button>
    <?php endforeach; ?>
  </div>

  <!-- PRICE MATRIX TABLE -->
  <div style="overflow-x:auto;" class="anim-fade-in-up">
    <div class="region-matrix" id="price-matrix">
      <!-- Header row -->
      <div class="matrix-cell matrix-crop-header matrix-header">Zao</div>
      <?php foreach ($regions as $r): ?>
      <div class="matrix-cell matrix-header"><?= esc($r['name']) ?></div>
      <?php endforeach; ?>

      <!-- Crop rows -->
      <?php foreach ($crops as $c): ?>
      <div class="matrix-cell matrix-crop-header crop-row" data-crop="<?= esc($c['id']) ?>">
        <?= $c['emoji'] ?> <?= esc($c['name_sw'] ?? $c['name']) ?>
      </div>
      <?php foreach ($regions as $r):
        $p = $price_matrix[$c['id']][$r['id']] ?? null; ?>
      <div class="matrix-cell crop-row" data-crop="<?= esc($c['id']) ?>">
        <?php if ($p): ?>
        <div class="matrix-price <?= trend_class($p['trend']) ?>">
          <?= number_format($p['price']) ?>
        </div>
        <div style="font-size:.68rem;color:var(--clr-text-muted)"><?= trend_arrow($p['trend']) ?><?= abs($p['change_pct']) ?>%</div>
        <?php else: ?>
        <span style="color:var(--clr-text-muted)">—</span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </div>

  <p style="font-size:.75rem;color:var(--clr-text-muted);margin-top:var(--space-4)">Bei ni kwa kilo 1 (TZS). Zilisasishwa: <?= date('d M Y, H:i') ?></p>

</div>
</div>
<?php render_layout_close(); ?>
<script>
function filterCrop(id, btn) {
  document.querySelectorAll('.crop-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.crop-row').forEach(el => {
    el.style.display = (id === 'all' || el.dataset.crop === id) ? '' : 'none';
  });
}
</script>
<?php render_foot(); ?>
