<?php
// loans.php — Agricultural Loans
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

$products = db_rows('SELECT * FROM loan_products WHERE active=1 ORDER BY min_amount ASC');
$my_loans = db_rows(
    'SELECT la.*, lp.name AS product_name, lp.emoji, lp.interest_rate, lp.tenure_months
     FROM loan_applications la JOIN loan_products lp ON lp.id=la.product_id
     WHERE la.user_id=? ORDER BY la.applied_at DESC',
    [$user['id']]
);

render_head('Mikopo ya Kilimo');
?>
<style>
.loan-product-card{background:var(--glass-bg);-webkit-backdrop-filter:var(--glass-blur);backdrop-filter:var(--glass-blur);border:var(--glass-border);border-radius:var(--radius-xl);padding:var(--space-6);display:flex;flex-direction:column;gap:var(--space-5);transition:all var(--tr-slow);}
.loan-product-card:hover{transform:translateY(-5px);border-color:var(--clr-border-bright);box-shadow:var(--glass-shadow),var(--shadow-glow-green);}
.loan-icon-wrap{width:56px;height:56px;border-radius:var(--radius-lg);background:rgba(58,143,74,.22);display:grid;place-items:center;font-size:1.8rem;}
.loan-range{display:flex;justify-content:space-between;background:var(--clr-surface-1);border-radius:var(--radius-md);padding:var(--space-3) var(--space-4);font-size:.82rem;}
.loan-feature{display:flex;align-items:center;gap:var(--space-2);font-size:.82rem;color:var(--clr-text-secondary);}
.loan-feature::before{content:"✓";color:var(--clr-green-300);font-weight:700;}
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:500;align-items:center;justify-content:center;}
.modal-backdrop.open{display:flex;}
.modal-box{background:var(--clr-bg-secondary);border:var(--glass-border);border-radius:var(--radius-xl);padding:var(--space-8);width:100%;max-width:500px;max-height:90vh;overflow-y:auto;position:relative;}
.calc-result{background:var(--clr-surface-1);border-radius:var(--radius-md);padding:var(--space-4);display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3);}
</style>
<?php render_layout_open(); render_sidebar($user, 'loans'); ?>
<div class="content-wrap">
<?php render_topbar($user, 'Mikopo ya Kilimo'); ?>
<div class="page-content">

  <div class="grid-3 mb-10">
    <?php foreach ($products as $p): ?>
    <div class="loan-product-card anim-fade-in-up">
      <div class="loan-icon-wrap"><?= $p['emoji'] ?></div>
      <div>
        <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;margin-bottom:var(--space-2)"><?= esc($p['name']) ?></h3>
        <p style="font-size:.85rem;color:var(--clr-text-muted)"><?= esc($p['description']) ?></p>
      </div>
      <div class="loan-range">
        <div><div style="font-size:.68rem;color:var(--clr-text-muted)">Kiwango cha Chini</div><div style="font-weight:700"><?= format_tzs($p['min_amount']) ?></div></div>
        <div style="text-align:right"><div style="font-size:.68rem;color:var(--clr-text-muted)">Kiwango cha Juu</div><div style="font-weight:700"><?= format_tzs($p['max_amount']) ?></div></div>
      </div>
      <div style="display:flex;gap:var(--space-4)">
        <div style="flex:1;background:var(--clr-surface-1);border-radius:var(--radius-md);padding:var(--space-3);text-align:center">
          <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;color:var(--clr-green-200)"><?= $p['interest_rate'] ?>%</div>
          <div style="font-size:.68rem;color:var(--clr-text-muted)">Riba ya Mwaka</div>
        </div>
        <div style="flex:1;background:var(--clr-surface-1);border-radius:var(--radius-md);padding:var(--space-3);text-align:center">
          <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;color:var(--clr-gold-400)"><?= $p['tenure_months'] ?> m</div>
          <div style="font-size:.68rem;color:var(--clr-text-muted)">Muda wa Marejesho</div>
        </div>
      </div>
      <div class="loan-feature"><?= esc($p['repayment_type']) ?></div>
      <div class="loan-feature"><?= esc($p['eligibility']) ?></div>
      <button class="btn btn-primary" onclick="openApply('<?= esc($p['id']) ?>','<?= esc(addslashes($p['name'])) ?>','<?= $p['min_amount'] ?>','<?= $p['max_amount'] ?>','<?= $p['interest_rate'] ?>','<?= $p['tenure_months'] ?>')">
        Omba Mkopo →
      </button>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- MY LOANS TABLE -->
  <?php if ($my_loans): ?>
  <div class="card anim-fade-in-up">
    <div class="card-header"><div class="card-title">📋 Maombi Yangu ya Mikopo</div></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Bidhaa</th><th>Kiasi</th><th>Riba</th><th>Muda</th><th>Hali</th><th>Tarehe</th></tr></thead>
        <tbody>
          <?php foreach ($my_loans as $l): ?>
          <tr>
            <td><?= $l['emoji'] ?> <?= esc($l['product_name']) ?></td>
            <td><?= format_tzs($l['amount']) ?></td>
            <td><?= $l['interest_rate'] ?>%</td>
            <td><?= $l['tenure_months'] ?> miezi</td>
            <td><span class="badge <?= badge_class($l['status']) ?>"><?= esc($l['status']) ?></span></td>
            <td><?= human_date($l['applied_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>
</div>

<!-- APPLICATION MODAL -->
<div class="modal-backdrop" id="apply-modal">
  <div class="modal-box">
    <button onclick="closeApply()" style="position:absolute;top:var(--space-4);right:var(--space-4);background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--clr-text-muted)">✕</button>
    <h2 style="font-family:var(--font-display);font-weight:700;margin-bottom:var(--space-6)" id="apply-title">Omba Mkopo</h2>
    <input type="hidden" id="apply-product-id"/>
    <input type="hidden" id="apply-rate"/>
    <input type="hidden" id="apply-tenure"/>
    <div class="form-group">
      <label class="form-label">Kiasi (TZS)</label>
      <input type="number" class="form-input" id="apply-amount" oninput="calcLoan()" placeholder="Weka kiasi unachotaka"/>
    </div>
    <div id="apply-calc" class="calc-result" style="display:none;margin-bottom:var(--space-4)">
      <div><div style="font-size:.72rem;color:var(--clr-text-muted)">Riba ya Mwaka</div><div id="c-interest" style="font-weight:700"></div></div>
      <div><div style="font-size:.72rem;color:var(--clr-text-muted)">Jumla Italipwa</div><div id="c-total" style="font-weight:700;color:var(--clr-green-200)"></div></div>
      <div><div style="font-size:.72rem;color:var(--clr-text-muted)">Malipo / Mwezi</div><div id="c-monthly" style="font-weight:700;color:var(--clr-gold-400)"></div></div>
    </div>
    <div class="form-group">
      <label class="form-label">Madhumuni</label>
      <textarea class="form-input" id="apply-purpose" rows="2" placeholder="Mfano: Kununua mbegu na mbolea kwa msimu wa mvua"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Ukubwa wa Shamba</label>
      <input type="text" class="form-input" id="apply-farm-size" placeholder="Mfano: Ekari 2"/>
    </div>
    <div class="form-group">
      <label class="form-label">Msimu wa Mazao</label>
      <input type="text" class="form-input" id="apply-season" placeholder="Mfano: Mvua ya Masika 2025"/>
    </div>
    <div class="form-group">
      <label class="form-label">Mdhamini</label>
      <input type="text" class="form-input" id="apply-guarantor" placeholder="Jina la mdhamini"/>
    </div>
    <p id="apply-error" style="color:var(--clr-red-400);font-size:.82rem;display:none"></p>
    <button class="btn btn-primary btn-full" onclick="submitApply()">Tuma Ombi →</button>
  </div>
</div>

<input type="hidden" id="csrf-token" value="<?= esc(csrf_token()) ?>">
<script>
const CSRF = document.getElementById('csrf-token').value;
let minAmt=0, maxAmt=0;

function openApply(id, name, min, max, rate, tenure) {
  minAmt=parseInt(min); maxAmt=parseInt(max);
  document.getElementById('apply-product-id').value=id;
  document.getElementById('apply-rate').value=rate;
  document.getElementById('apply-tenure').value=tenure;
  document.getElementById('apply-title').textContent='Omba — '+name;
  document.getElementById('apply-amount').min=min; document.getElementById('apply-amount').max=max;
  document.getElementById('apply-modal').classList.add('open');
}
function closeApply(){document.getElementById('apply-modal').classList.remove('open');}
function calcLoan(){
  const amt=parseFloat(document.getElementById('apply-amount').value)||0;
  const rate=parseFloat(document.getElementById('apply-rate').value)/100;
  const months=parseInt(document.getElementById('apply-tenure').value);
  if(amt<=0){document.getElementById('apply-calc').style.display='none';return;}
  const totalInterest=amt*rate*(months/12);
  const total=amt+totalInterest;
  const monthly=total/months;
  document.getElementById('c-interest').textContent='TZS '+Math.round(totalInterest).toLocaleString();
  document.getElementById('c-total').textContent='TZS '+Math.round(total).toLocaleString();
  document.getElementById('c-monthly').textContent='TZS '+Math.round(monthly).toLocaleString();
  document.getElementById('apply-calc').style.display='grid';
}
async function submitApply(){
  const err=document.getElementById('apply-error');
  err.style.display='none';
  const amt=parseFloat(document.getElementById('apply-amount').value)||0;
  if(amt<minAmt||amt>maxAmt){err.textContent='Kiasi kiwe kati ya TZS '+minAmt.toLocaleString()+' na TZS '+maxAmt.toLocaleString();err.style.display='block';return;}
  const res=await fetch('api/loans.php',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
    body:JSON.stringify({action:'apply',csrf_token:CSRF,product_id:document.getElementById('apply-product-id').value,
      amount:amt,purpose:document.getElementById('apply-purpose').value,
      farm_size:document.getElementById('apply-farm-size').value,
      crop_season:document.getElementById('apply-season').value,
      guarantor:document.getElementById('apply-guarantor').value})});
  const data=await res.json();
  if(data.ok){closeApply();alert(data.message);location.reload();}
  else{err.textContent=data.error;err.style.display='block';}
}
</script>
<?php render_layout_close(); render_foot(); ?>
