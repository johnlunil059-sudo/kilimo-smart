<?php
// storage.php — Storage Hubs
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

$hubs   = db_rows('SELECT sh.*, r.name AS region_name FROM storage_hubs sh JOIN regions r ON r.id=sh.region_id ORDER BY sh.status ASC, sh.region_id');
$crops  = db_rows('SELECT * FROM crops ORDER BY name');

// My bookings
$my_bookings = db_rows(
    'SELECT sb.*, sh.name AS hub_name, sh.region_id, c.name AS crop_name, c.emoji
     FROM storage_bookings sb JOIN storage_hubs sh ON sh.id=sb.hub_id JOIN crops c ON c.id=sb.crop_id
     WHERE sb.user_id = ? ORDER BY sb.created_at DESC LIMIT 10',
    [$user['id']]
);

render_head('Uhifadhi wa Mazao');
?>
<style>
.hub-card{background:var(--glass-bg);-webkit-backdrop-filter:var(--glass-blur);backdrop-filter:var(--glass-blur);border:var(--glass-border);border-radius:var(--radius-xl);padding:var(--space-6);transition:all var(--tr-slow);display:flex;flex-direction:column;gap:var(--space-4);}
.hub-card:hover{transform:translateY(-5px);box-shadow:var(--glass-shadow),var(--shadow-glow-green);border-color:var(--clr-border-bright);}
.hub-card.full{opacity:.75;}
.hub-card.full:hover{transform:none;}
.hub-icon{width:52px;height:52px;border-radius:var(--radius-lg);background:rgba(58,143,74,.22);display:grid;place-items:center;font-size:1.5rem;flex-shrink:0;}
.hub-metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-3);background:var(--clr-surface-1);border-radius:var(--radius-lg);padding:var(--space-4);}
.hub-metric-value{font-family:var(--font-display);font-size:1.1rem;font-weight:700;}
.hub-metric-label{font-size:.68rem;color:var(--clr-text-muted);margin-top:2px;}
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:500;align-items:center;justify-content:center;}
.modal-backdrop.open{display:flex;}
.modal-box{background:var(--clr-bg-secondary);border:var(--glass-border);border-radius:var(--radius-xl);padding:var(--space-8);width:100%;max-width:480px;position:relative;}
</style>
<?php render_layout_open(); render_sidebar($user, 'storage'); ?>
<div class="content-wrap">
<?php render_topbar($user, 'Uhifadhi wa Mazao'); ?>
<div class="page-content">

  <!-- SUMMARY -->
  <div class="grid-4 mb-8">
    <?php
    $total_cap  = array_sum(array_column($hubs,'capacity'));
    $total_occ  = array_sum(array_column($hubs,'occupied'));
    $active_cnt = count(array_filter($hubs, fn($h)=>$h['status']==='active'));
    $avg_price  = count($hubs) ? round(array_sum(array_column($hubs,'price_per_tonne'))/count($hubs)) : 0;
    $kpis = [
      ['label'=>'Jumla ya Maghala','value'=>count($hubs),'sub'=>$active_cnt.' Yanafanya kazi','icon'=>'🏪','color'=>'green'],
      ['label'=>'Uwezo wa Jumla','value'=>number_format($total_cap).' t','sub'=>number_format($total_occ).' t imehifadhiwa','icon'=>'📦','color'=>'gold'],
      ['label'=>'Nafasi Iliyobaki','value'=>number_format($total_cap-$total_occ).' t','sub'=>hub_occupancy_pct($total_occ,$total_cap).'% imejaa','icon'=>'📊','color'=>'sky'],
      ['label'=>'Bei ya Wastani','value'=>format_tzs($avg_price),'sub'=>'kwa tani/mwezi','icon'=>'💰','color'=>'orange'],
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

  <!-- HUBS GRID -->
  <h2 style="font-family:var(--font-display);font-size:1.3rem;font-weight:700;margin-bottom:var(--space-6)">Maghala Yanayopatikana</h2>
  <div class="grid-2 mb-8">
    <?php foreach ($hubs as $h):
      $pct = hub_occupancy_pct((int)$h['occupied'], (int)$h['capacity']); ?>
    <div class="hub-card <?= $h['status'] === 'full' ? 'full' : '' ?> anim-fade-in-up">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-3)">
        <div style="display:flex;align-items:center;gap:var(--space-4)">
          <div class="hub-icon">🏪</div>
          <div>
            <div style="font-family:var(--font-display);font-size:1.05rem;font-weight:700"><?= esc($h['name']) ?></div>
            <div style="font-size:.78rem;color:var(--clr-text-muted)">📍 <?= esc($h['location']) ?></div>
          </div>
        </div>
        <span class="badge <?= $h['status']==='active'?'badge-green':($h['status']==='full'?'badge-red':'badge-muted') ?>"><?= esc($h['status']) ?></span>
      </div>

      <div class="hub-metrics">
        <div class="hub-metric-item" style="text-align:center">
          <div class="hub-metric-value"><?= number_format($h['capacity']) ?>t</div>
          <div class="hub-metric-label">Uwezo</div>
        </div>
        <div class="hub-metric-item" style="text-align:center">
          <div class="hub-metric-value"><?= $h['temperature'] ?>°C</div>
          <div class="hub-metric-label">Joto</div>
        </div>
        <div class="hub-metric-item" style="text-align:center">
          <div class="hub-metric-value"><?= $h['humidity'] ?>%</div>
          <div class="hub-metric-label">Unyevu</div>
        </div>
      </div>

      <div>
        <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--clr-text-muted);margin-bottom:var(--space-2)">
          <span>Imejaa</span><span class="fw-700 <?= $pct>=90?'text-red-400':'' ?>"><?= $pct ?>%</span>
        </div>
        <div class="progress-bar-wrap"><div class="progress-bar-fill <?= progress_fill_class($pct) ?>"></div></div>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <div style="font-size:.72rem;color:var(--clr-text-muted)">Bei</div>
          <div style="font-family:var(--font-display);font-weight:700;color:var(--clr-text-accent)"><?= format_tzs($h['price_per_tonne']) ?>/t/mwezi</div>
        </div>
        <?php if ($h['status'] !== 'full'): ?>
        <button class="btn btn-primary btn-sm" onclick="openBook('<?= esc($h['id']) ?>','<?= esc($h['name']) ?>','<?= number_format($h['price_per_tonne']) ?>')">
          Hifadhi Nafasi →
        </button>
        <?php else: ?>
        <span class="badge badge-red">Imejaa</span>
        <?php endif; ?>
      </div>

      <div style="font-size:.75rem;color:var(--clr-text-muted)">
        Mazao: <?= esc($h['crops_stored'] ?? '') ?> · Msimamizi: <?= esc($h['manager'] ?? '') ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- MY BOOKINGS -->
  <?php if ($my_bookings): ?>
  <div class="card anim-fade-in-up">
    <div class="card-header"><div class="card-title">📋 Mikataba Yangu</div></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Ghala</th><th>Zao</th><th>Tani</th><th>Gharama</th><th>Tarehe ya Mwisho</th><th>Hali</th></tr></thead>
        <tbody>
          <?php foreach ($my_bookings as $b): ?>
          <tr>
            <td><?= esc($b['hub_name']) ?></td>
            <td><?= $b['emoji'] ?> <?= esc($b['crop_name']) ?></td>
            <td><?= $b['tonnes'] ?>t</td>
            <td><?= format_tzs($b['total_cost']) ?></td>
            <td><?= $b['end_date'] ? human_date($b['end_date']) : '—' ?></td>
            <td><span class="badge <?= badge_class($b['status']) ?>"><?= esc($b['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>
</div>

<!-- BOOKING MODAL -->
<div class="modal-backdrop" id="book-modal">
  <div class="modal-box">
    <button onclick="closeBook()" style="position:absolute;top:var(--space-4);right:var(--space-4);background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--clr-text-muted)">✕</button>
    <h2 style="font-family:var(--font-display);font-weight:700;margin-bottom:var(--space-6)" id="book-title">Hifadhi Nafasi</h2>
    <input type="hidden" id="book-hub-id"/>
    <div class="form-group">
      <label class="form-label">Zao</label>
      <select class="form-input" id="book-crop">
        <option value="">— Chagua Zao —</option>
        <?php foreach ($crops as $c): ?>
        <option value="<?= esc($c['id']) ?>"><?= $c['emoji'] ?> <?= esc($c['name_sw'] ?? $c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Tani za Kuhifadhi</label>
      <input type="number" class="form-input" id="book-tonnes" min="0.1" step="0.1" placeholder="Mfano: 5"/>
    </div>
    <div class="form-group">
      <label class="form-label">Miezi</label>
      <select class="form-input" id="book-months">
        <?php for ($i=1;$i<=12;$i++): ?><option value="<?=$i?>"><?=$i?> mwezi<?=$i>1?'':'';?></option><?php endfor; ?>
      </select>
    </div>
    <div id="book-cost-preview" style="background:var(--clr-surface-1);border-radius:var(--radius-md);padding:var(--space-4);margin-bottom:var(--space-4);display:none">
      <div style="font-size:.82rem;color:var(--clr-text-muted);margin-bottom:var(--space-1)">Gharama ya Jumla</div>
      <div id="book-cost-val" style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--clr-text-accent)"></div>
    </div>
    <p id="book-error" style="color:var(--clr-red-400);font-size:.82rem;display:none"></p>
    <button class="btn btn-primary btn-full" onclick="submitBook()">Thibitisha Uhifadhi →</button>
  </div>
</div>

<input type="hidden" id="csrf-token" value="<?= esc(csrf_token()) ?>">
<script>
const CSRF = document.getElementById('csrf-token').value;
let currentHubPrice = 0;

function openBook(id, name, price) {
  currentHubPrice = parseInt(price.replace(/,/g,''));
  document.getElementById('book-hub-id').value = id;
  document.getElementById('book-title').textContent = 'Hifadhi Nafasi — ' + name;
  document.getElementById('book-modal').classList.add('open');
  calcCost();
}
function closeBook() { document.getElementById('book-modal').classList.remove('open'); }
function calcCost() {
  const t = parseFloat(document.getElementById('book-tonnes').value)||0;
  const m = parseInt(document.getElementById('book-months').value)||1;
  const total = t * currentHubPrice * m;
  const el = document.getElementById('book-cost-preview');
  if (t > 0) { el.style.display=''; document.getElementById('book-cost-val').textContent = 'TZS ' + total.toLocaleString(); }
  else el.style.display='none';
}
document.getElementById('book-tonnes').addEventListener('input', calcCost);
document.getElementById('book-months').addEventListener('change', calcCost);

async function submitBook() {
  const err = document.getElementById('book-error');
  err.style.display='none';
  const res = await fetch('api/storage.php', {
    method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
    body: JSON.stringify({
      action:'book', csrf_token:CSRF,
      hub_id: document.getElementById('book-hub-id').value,
      crop_id: document.getElementById('book-crop').value,
      tonnes: document.getElementById('book-tonnes').value,
      months: document.getElementById('book-months').value,
    })
  });
  const data = await res.json();
  if (data.ok) { closeBook(); alert(data.message); location.reload(); }
  else { err.textContent=data.error; err.style.display='block'; }
}
</script>
<?php render_layout_close(); render_foot(); ?>
