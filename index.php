<?php
// ============================================================
// index.php — Landing Page + Auth
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
session_start_safe();

// Parse JSON body into $_POST so get_input() and csrf_verify() work
$raw=file_get_contents('php://input');
if($raw){
  $json=json_decode($raw, true);
  if(is_array($json))$_POST=array_merge($_POST,$json);
}
// Redirect if already logged in
$user = get_session();
if ($user) {
    redirect($user['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
}

// Handle POST (login / register) via AJAX
if (is_post() && is_ajax()) {
    if (!csrf_verify()) { json_response(['ok' => false, 'error' => 'Ombi si salama.'], 403); }
    $action = get_input('action');
    if ($action === 'login') {
        $result = auth_login(get_input('email'), get_input('password'));
        if ($result['ok']) {
            $result['redirect'] = $result['user']['role'] === 'admin' ? 'admin.php' : 'dashboard.php';
        }
        json_response($result);
    }
    if ($action === 'register') {
        $result = auth_register(get_input('name'), get_input('phone'), get_input('email'), get_input('region'), get_input('password'));
        if ($result['ok']) {
            $result['redirect'] = 'dashboard.php';
        }
        json_response($result);
    }
    json_response(['ok' => false, 'error' => 'Hatua haijulikani.'], 400);
}

$regions = [
    'Dar es Salaam','Morogoro','Iringa','Dodoma',
    'Arusha','Mbeya','Mwanza','Tanga','Kigoma',
    'Tabora','Singida','Shinyanga','Mara','Kagera',
];
?>
<!doctype html>
<html lang="sw">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Kilimo Smart — Jukwaa la kilimo la kisasa kwa wakulima wa Tanzania. Bei za mazao, hali ya hewa, hifadhi, na mikopo yote mahali pamoja."/>
  <title>Kilimo Smart — Teknolojia ya Kilimo Tanzania</title>
  <link rel="stylesheet" href="css/main.css"/>
  <link rel="stylesheet" href="css/animations.css"/>
  <style>
    body { overflow-x: hidden; }
    .landing-hero {
      min-height: 100vh;
      background: var(--gradient-hero);
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      gap: var(--space-12);
      padding: var(--space-8) var(--space-16);
      position: relative;
      overflow: hidden;
    }
    .landing-hero::before {
      content:"";position:absolute;inset:0;
      background: radial-gradient(ellipse 60% 50% at 70% 50%, rgba(39,105,52,.25) 0%, transparent 70%),
                  radial-gradient(ellipse 30% 30% at 20% 80%, rgba(232,184,48,.12) 0%, transparent 60%);
    }
    .hero-text { position:relative; z-index:2; }
    .hero-badge {
      display:inline-flex;align-items:center;gap:var(--space-2);
      background:rgba(58,143,74,.18);border:1px solid rgba(82,181,100,.35);
      border-radius:var(--radius-full);padding:var(--space-2) var(--space-4);
      font-size:.78rem;font-weight:600;color:var(--clr-green-200);
      letter-spacing:.06em;text-transform:uppercase;margin-bottom:var(--space-6);
    }
    .hero-title { font-family:var(--font-display);font-size:clamp(2.8rem,5vw,4.2rem);font-weight:800;line-height:1.1;margin-bottom:var(--space-6); }
    .hero-title .highlight { background:linear-gradient(135deg,#52b564 0%,#e8b830 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }
    .hero-subtitle { font-size:1.1rem;color:var(--clr-text-secondary);max-width:480px;line-height:1.7;margin-bottom:var(--space-10); }
    .hero-actions { display:flex;gap:var(--space-4);flex-wrap:wrap; }
    .hero-stats { display:flex;gap:var(--space-8);margin-top:var(--space-12);padding-top:var(--space-8);border-top:var(--glass-border); }
    .hero-stat-value { font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--clr-text-accent); }
    .hero-stat-label { font-size:.78rem;color:var(--clr-text-muted);margin-top:2px; }
    .hero-auth-card {
      position:relative;z-index:2;
      background:var(--glass-bg);-webkit-backdrop-filter:var(--glass-blur);backdrop-filter:var(--glass-blur);
      border:var(--glass-border);border-radius:var(--radius-xl);box-shadow:var(--glass-shadow);
      padding:var(--space-8);max-width:440px;width:100%;margin-left:auto;
    }
    .auth-header { text-align:center;margin-bottom:var(--space-8); }
    .auth-logo {
      width:60px;height:60px;background:var(--gradient-green);border-radius:var(--radius-lg);
      display:grid;place-items:center;font-size:1.8rem;margin:0 auto var(--space-4);
      box-shadow:var(--shadow-glow-green);animation:floatUp 4s ease-in-out infinite;
    }
    .auth-title { font-size:1.4rem;font-weight:700; }
    .auth-sub { font-size:.85rem;color:var(--clr-text-muted);margin-top:var(--space-2); }
    .auth-tabs {
      display:grid;grid-template-columns:1fr 1fr;
      background:var(--clr-surface-1);border-radius:var(--radius-md);padding:4px;margin-bottom:var(--space-6);
    }
    .auth-tab { padding:var(--space-3);border-radius:var(--radius-sm);text-align:center;font-size:.88rem;font-weight:600;color:var(--clr-text-muted);cursor:pointer;transition:all var(--tr-base);border:none;background:none; }
    .auth-tab.active { background:var(--gradient-green);color:#fff;box-shadow:var(--shadow-sm); }
    .auth-panel { display:none;animation:fadeIn .3s ease; }
    .auth-panel.active { display:block; }
    .demo-hint {
      margin-top:var(--space-4);padding:var(--space-3) var(--space-4);
      background:rgba(232,184,48,.1);border:1px solid rgba(232,184,48,.2);
      border-radius:var(--radius-md);font-size:.75rem;color:var(--clr-text-muted);
      display:flex;align-items:center;gap:var(--space-2);
    }
    .landing-features { padding:var(--space-16);background:var(--clr-bg-secondary); }
    .landing-nav {
      position:fixed;top:0;left:0;right:0;z-index:200;
      padding:var(--space-4) var(--space-8);display:flex;align-items:center;justify-content:space-between;
      background:rgba(10,31,14,.8);-webkit-backdrop-filter:blur(12px);backdrop-filter:blur(12px);
      border-bottom:1px solid rgba(82,181,100,.12);
    }
    .nav-logo-group { display:flex;align-items:center;gap:var(--space-3);text-decoration:none; }
    .nav-logo-icon { width:34px;height:34px;background:var(--gradient-green);border-radius:var(--radius-md);display:grid;place-items:center;font-size:1.1rem; }
    .nav-logo-text { font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--clr-text-primary); }
    .nav-links { display:flex;gap:var(--space-8); }
    .nav-links a { font-size:.88rem;font-weight:500;color:var(--clr-text-secondary);text-decoration:none;transition:color var(--tr-base); }
    .nav-links a:hover { color:var(--clr-text-primary); }
    .error-msg { color:var(--clr-red-400);font-size:.82rem;margin-top:var(--space-3);display:none; }
    @media(max-width:900px){
      .landing-hero{grid-template-columns:1fr;padding:var(--space-8);padding-top:100px;}
      .hero-auth-card{margin:0 auto;max-width:100%;}
      .hero-stats{flex-wrap:wrap;gap:var(--space-4);}
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav class="landing-nav">
  <a href="index.php" class="nav-logo-group">
    <img src="/files3/images/logo.png" alt="Kilimo Smart" height:="40" width:="40" style="border-radius:50%;border:3px solid var(--clr-green-400);padding:4px;background:white;object-fit:cover;">
    <span class="nav-logo-text">Kilimo Smart</span>
  </a>
  <div class="nav-links">
    <a href="#features">Huduma</a>
    <a href="#about">Kuhusu</a>
    <a href="about.php">Timu</a>
  </div>
  <a href="#auth" class="btn btn-primary btn-sm">Ingia Sasa</a>
</nav>

<!-- HERO -->
<section class="landing-hero" id="auth" style="padding-top:80px;">
  <!-- LEFT TEXT -->
  <div class="hero-text anim-fade-in-up">
    <div class="hero-badge">🌍 Teknolojia ya Kilimo · Tanzania</div>
    <h1 class="hero-title">
      Ukulima Hodari<br/>
      Unaanza<br/>
      <span class="highlight">Hapa.</span>
    </h1>
    <p class="hero-subtitle">
      Pata bei za mazao kwa wakati halisi, hali ya hewa, hifadhi ya mazao, na mikopo — yote kupitia simu yako. Kilimo Smart inaunganisha wakulima wadogo wa Tanzania na masoko ya kuaminika.
    </p>
    <div class="hero-actions">
      <a href="#auth" class="btn btn-primary btn-lg">Anza Sasa — Bure</a>
      <a href="#features" class="btn btn-outline btn-lg">Jua Zaidi</a>
    </div>
    <div class="hero-stats">
      <div>
        <div class="hero-stat-value">487+</div>
        <div class="hero-stat-label">Wakulima Waliojisajili</div>
      </div>
      <div>
        <div class="hero-stat-value">8</div>
        <div class="hero-stat-label">Mikoa Inayohudumika</div>
      </div>
      <div>
        <div class="hero-stat-value">4</div>
        <div class="hero-stat-label">Maghala ya Jamii</div>
      </div>
    </div>
  </div>

  <!-- RIGHT AUTH CARD -->
  <div class="hero-auth-card anim-fade-in-up" style="animation-delay:.2s">
    <div class="auth-header">
      <img src="/files3/images/logo.png" alt="Kilimo Smart" height:="80" width:="80" style="border-radius:50%;border:2px solid var(--clr-green-400);padding:4px;background:white;object-fit:cover;">
      <h2 class="auth-title">Karibu Kilimo Smart</h2>
      <p class="auth-sub">Ingia au jisajili kupata huduma zote</p>
    </div>

    <div class="auth-tabs">
      <button class="auth-tab active" onclick="switchTab('login')">Ingia</button>
      <button class="auth-tab" onclick="switchTab('register')">Jisajili</button>
    </div>

    <!-- LOGIN -->
    <div id="panel-login" class="auth-panel active">
      <div class="form-group">
        <label class="form-label">Barua Pepe</label>
        <input type="email" id="login-email" class="form-input" placeholder="jina@mfano.tz" autocomplete="email"/>
      </div>
      <div class="form-group">
        <label class="form-label">Nywila</label>
        <input type="password" id="login-password" class="form-input" placeholder="Nywila yako" autocomplete="current-password"/>
      </div>
      <p id="login-error" class="error-msg"></p>
      <button class="btn btn-primary btn-full" onclick="doLogin()">Ingia →</button>
      <div class="demo-hint">
        💡 <span><strong>Demo:</strong> demo@kilimosmart.tz / demo &nbsp;|&nbsp; Admin: yohana@kilimosmart.tz / admin123</span>
      </div>
    </div>

    <!-- REGISTER -->
    <div id="panel-register" class="auth-panel">
      <div class="form-group">
        <label class="form-label">Jina Kamili</label>
        <input type="text" id="reg-name" class="form-input" placeholder="Jina lako kamili"/>
      </div>
      <div class="form-group">
        <label class="form-label">Nambari ya Simu</label>
        <input type="tel" id="reg-phone" class="form-input" placeholder="+255 7XX XXX XXX"/>
      </div>
      <div class="form-group">
        <label class="form-label">Barua Pepe</label>
        <input type="email" id="reg-email" class="form-input" placeholder="barua@mfano.tz"/>
      </div>
      <div class="form-group">
        <label class="form-label">Mkoa</label>
        <select id="reg-region" class="form-input">
          <option value="">— Chagua Mkoa —</option>
          <?php foreach ($regions as $r): ?>
          <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Nywila</label>
        <input type="password" id="reg-password" class="form-input" placeholder="Herufi 6 au zaidi"/>
      </div>
      <p id="register-error" class="error-msg"></p>
      <button class="btn btn-primary btn-full" onclick="doRegister()">Jisajili Bure →</button>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section id="features" class="landing-features">
  <div style="text-align:center;margin-bottom:var(--space-12)">
    <span class="section-eyebrow">Huduma Zetu</span>
    <h2 class="section-heading-large" style="margin-top:var(--space-4)">Kila Kitu Unachohitaji kwa Kilimo Bora</h2>
  </div>
  <div class="grid-4">
    <?php
    $features = [
      ['icon'=>'📊','title'=>'Bei za Mazao','desc'=>'Bei za soko kwa wakati halisi kutoka mikoa 8 ya Tanzania. Mazao 8 ya kawaida.','color'=>'green'],
      ['icon'=>'⛅','title'=>'Hali ya Hewa','desc'=>'Utabiri wa siku 7 na arifa za hali ya hewa kwa mkulima.','color'=>'sky'],
      ['icon'=>'🏪','title'=>'Hifadhi ya Mazao','desc'=>'Hifadhi mazao yako kwenye maghala ya kisasa. Punguza hasara baada ya mavuno.','color'=>'gold'],
      ['icon'=>'💳','title'=>'Mikopo ya Kilimo','desc'=>'Omba mkopo rahisi kwa mazao, zana, au hifadhi kupitia simu yako.','color'=>'green'],
    ];
    foreach ($features as $f): ?>
    <div class="feature-card">
      <div class="feature-icon <?= $f['color'] ?>"><?= $f['icon'] ?></div>
      <h3 class="feature-title"><?= esc($f['title']) ?></h3>
      <p class="feature-desc"><?= esc($f['desc']) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- MISSION / VISION / VALUES -->
<section id="about" style="padding:var(--space-16);background:var(--clr-bg-secondary);">
  <div style="text-align:center;margin-bottom:var(--space-12);">
    <span class="section-eyebrow">Kuhusu Kilimo Smart</span>
    <h2 class="section-heading-large" style="margin-top:var(--space-4);">Dhamira, Maono na Maadili Yetu</h2>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-8);max-width:1000px;margin:0 auto var(--space-12);">
    <div class="feature-card" style="border-left:3px solid var(--clr-green-400);">
      <div class="feature-icon green" style="font-size:1.8rem;">🌟</div>
      <h3 class="feature-title">Maono (Vision)</h3>
      <p class="feature-desc">Kuhakikisha kila mkulima Tanzania anakuwa mkulima hodari anayejua bei za soko, anapata masoko ya kuaminika, na analima kwa faida zaidi.</p>
    </div>
    <div class="feature-card" style="border-left:3px solid var(--clr-gold-400);">
      <div class="feature-icon gold" style="font-size:1.8rem;">🎯</div>
      <h3 class="feature-title">Dhamira (Mission)</h3>
      <p class="feature-desc">Kutoa teknolojia rahisi na ya bei nafuu kwa wakulima wadogo, kuwasilisha kwenye masoko ya kuaminika, na kubadilisha kilimo kuwa biashara yenye tija na heshima.</p>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:var(--space-4);max-width:1100px;margin:0 auto;">
    <?php
    $values = [
      ['🤝','Urahisi','Rahisi kutumia kwa wakulima wa ngazi zote'],
      ['🔍','Uwazi','Taarifa za soko wazi na za kweli'],
      ['💪','Ustahimilivu','Imejengwa kustahimili changamoto'],
      ['⚖️','Usawa','Inapatikana kwa wote, hasa wanawake na vijana'],
      ['💡','Ubunifu','Inaboresha daima kupitia teknolojia ya kisasa'],
    ];
    foreach ($values as [$icon,$name,$desc]): ?>
    <div class="feature-card" style="text-align:center;padding:var(--space-6);">
      <div style="font-size:2rem;margin-bottom:var(--space-3);"><?= $icon ?></div>
      <div style="font-family:var(--font-display);font-weight:700;margin-bottom:var(--space-2);color:var(--clr-green-200);"><?= esc($name) ?></div>
      <div style="font-size:.8rem;color:var(--clr-text-muted);"><?= esc($desc) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <div style="text-align:center;margin-top:var(--space-10);">
    <a href="about.php" class="btn btn-outline btn-lg">Jua Zaidi Kuhusu Sisi →</a>
  </div>
</section>

<!-- FOOTER -->
<footer style="background:var(--clr-bg-primary);border-top:var(--glass-border);padding:var(--space-8) var(--space-16);">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:var(--space-6);">
    <div>
      <div style="font-family:var(--font-display);font-weight:800;font-size:1.1rem;margin-bottom:var(--space-2);">🌿 Kilimo Smart</div>
      <div style="font-size:.78rem;color:var(--clr-text-muted);">Farm Smart, Sell in Your Hand.</div>
    </div>
    <div style="display:flex;gap:var(--space-8);">
      <div>
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--clr-text-muted);margin-bottom:var(--space-3);">Huduma</div>
        <?php foreach(['Bei za Mazao'=>'market.php','Hali ya Hewa'=>'weather.php','Hifadhi'=>'storage.php','Mikopo'=>'loans.php'] as $label=>$href): ?>
        <div><a href="<?= $href ?>" style="font-size:.82rem;color:var(--clr-text-secondary);text-decoration:none;"><?= esc($label) ?></a></div>
        <?php endforeach; ?>
      </div>
      <div>
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--clr-text-muted);margin-bottom:var(--space-3);">Kampuni</div>
        <?php foreach(['Kuhusu Sisi'=>'about.php'] as $label=>$href): ?>
        <div><a href="<?= $href ?>" style="font-size:.82rem;color:var(--clr-text-secondary);text-decoration:none;"><?= esc($label) ?></a></div>
        <?php endforeach; ?>
      </div>
      <div>
        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--clr-text-muted);margin-bottom:var(--space-3);">Mawasiliano</div>
        <div style="font-size:.82rem;color:var(--clr-text-secondary);">info@kilimosmart.tz</div>
        <div style="font-size:.82rem;color:var(--clr-text-secondary);">+255 754 000 000</div>
      </div>
    </div>
  </div>
  <div style="border-top:var(--glass-border);margin-top:var(--space-6);padding-top:var(--space-6);font-size:.75rem;color:var(--clr-text-muted);text-align:center;">
    © <?= date('Y') ?> Kilimo Smart Tanzania Ltd. · Haki zote zimehifadhiwa.
  </div>
</footer>

<input type="hidden" id="csrf-token" value="<?= esc(csrf_token()) ?>">
<script>
const CSRF = document.getElementById('csrf-token').value;

function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach((t,i) => t.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
  document.getElementById('panel-login').classList.toggle('active', tab==='login');
  document.getElementById('panel-register').classList.toggle('active', tab==='register');
}

async function post(data) {
  const fd = new FormData();
  for(const[k,v] of Object.entries(data)) fd.append(k,v);
  fd.append('csrf_token',CSRF);
  const r=await fetch('index.php',{
    method:'POST',
    headers:{'X-Requested-Width':'XMLHttpRequest'},
    body:fd
  });
  return r.json();
}

async function doLogin() {
  const err = document.getElementById('login-error');
  err.style.display='none';
  const res = await post({action:'login', email:document.getElementById('login-email').value, password:document.getElementById('login-password').value});
  if (res.ok) { window.location.href = res.redirect; }
  else { err.textContent = res.error; err.style.display='block'; }
}

async function doRegister() {
  const err = document.getElementById('register-error');
  err.style.display='none';
  const res = await post({action:'register', name:document.getElementById('reg-name').value,
    phone:document.getElementById('reg-phone').value, email:document.getElementById('reg-email').value,
    region:document.getElementById('reg-region').value, password:document.getElementById('reg-password').value});
  if (res.ok) { window.location.href = res.redirect; }
  else { err.textContent = res.error; err.style.display='block'; }
}

document.querySelectorAll('.form-input').forEach(el => el.addEventListener('keydown', e => { if(e.key==='Enter') { const panel=el.closest('.auth-panel'); if(panel.id==='panel-login') doLogin(); else doRegister(); } }));
</script>
</body>
</html>
