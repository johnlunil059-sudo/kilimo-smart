<?php
// weather.php — Hali ya Hewa
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

// Load active weather alerts from DB
$alerts = db_rows('SELECT wa.*, r.name AS region_name FROM weather_alerts wa JOIN regions r ON r.id = wa.region_id WHERE wa.active = 1 ORDER BY wa.created_at DESC');

render_head('Hali ya Hewa', '<style>
.region-tabs { display:flex; gap:var(--space-2); flex-wrap:wrap; margin-bottom:var(--space-8); }
.region-tab-btn { padding:var(--space-2) var(--space-5); border-radius:var(--radius-full); font-size:.82rem; font-weight:600; border:1px solid var(--clr-border); background:var(--clr-surface-1); color:var(--clr-text-muted); cursor:pointer; transition:all var(--tr-base); }
.region-tab-btn.active, .region-tab-btn:hover { background:var(--gradient-sky); color:#fff; border-color:transparent; }
.weather-hero-card { background:linear-gradient(135deg,#0d3b6e 0%,#1a6fa0 50%,#0d3b6e 100%); border-radius:var(--radius-xl); padding:var(--space-10); display:flex; align-items:center; gap:var(--space-10); margin-bottom:var(--space-8); position:relative; overflow:hidden; }
.weather-hero-card::before { content:""; position:absolute; inset:0; background:radial-gradient(ellipse 50% 80% at 70% 50%, rgba(255,255,255,.06) 0%, transparent 70%); }
.weather-main-icon { font-size:5rem; animation:floatUp 4s ease-in-out infinite; position:relative; z-index:1; }
.weather-main-info { position:relative; z-index:1; }
.weather-main-temp { font-family:var(--font-display); font-size:4rem; font-weight:800; color:#fff; line-height:1; }
.weather-main-label { font-size:1.1rem; color:rgba(255,255,255,.75); margin-top:var(--space-2); }
.weather-main-region { font-size:.9rem; color:rgba(255,255,255,.55); margin-top:var(--space-1); }
.weather-meta-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:var(--space-4); position:relative; z-index:1; margin-left:auto; }
.weather-meta-item { text-align:center; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); border-radius:var(--radius-lg); padding:var(--space-4); }
.meta-icon { font-size:1.4rem; margin-bottom:var(--space-2); }
.meta-value { font-family:var(--font-display); font-size:1.2rem; font-weight:700; color:#fff; }
.meta-label { font-size:.72rem; color:rgba(255,255,255,.55); margin-top:2px; }
.forecast-7day { display:grid; grid-template-columns:repeat(7,1fr); gap:var(--space-3); }
.forecast-card { background:var(--glass-bg); -webkit-backdrop-filter:var(--glass-blur); backdrop-filter:var(--glass-blur); border:var(--glass-border); border-radius:var(--radius-lg); padding:var(--space-5); text-align:center; transition:all var(--tr-base); }
.forecast-card:hover { transform:translateY(-4px); border-color:var(--clr-border-bright); box-shadow:var(--shadow-md); }
.forecast-card.today { background:var(--gradient-sky); border-color:rgba(52,152,219,.5); }
.fc-day { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--clr-text-muted); margin-bottom:var(--space-3); }
.forecast-card.today .fc-day { color:rgba(255,255,255,.7); }
.fc-icon { font-size:2rem; margin-bottom:var(--space-3); }
.fc-high { font-family:var(--font-display); font-size:1.4rem; font-weight:800; }
.fc-low { font-size:.8rem; color:var(--clr-text-muted); margin-top:2px; }
.forecast-card.today .fc-low { color:rgba(255,255,255,.6); }
.fc-rain { font-size:.72rem; margin-top:var(--space-2); display:flex; align-items:center; justify-content:center; gap:4px; }
.fc-rain-bar { width:100%; height:3px; background:rgba(52,152,219,.2); border-radius:2px; margin-top:4px; }
.fc-rain-fill { height:100%; background:var(--clr-sky-400); border-radius:2px; }
.agri-advice-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:var(--space-5); }
.agri-advice-card { background:var(--clr-surface-1); border:var(--glass-border); border-radius:var(--radius-lg); padding:var(--space-5); }
.advice-icon-wrap { width:40px; height:40px; border-radius:var(--radius-md); display:grid; place-items:center; font-size:1.2rem; margin-bottom:var(--space-3); }
@media(max-width:1024px){ .forecast-7day{grid-template-columns:repeat(4,1fr);} .weather-meta-grid{grid-template-columns:repeat(2,1fr);} .agri-advice-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:640px){ .weather-hero-card{flex-direction:column;padding:var(--space-6);} .forecast-7day{grid-template-columns:repeat(2,1fr);} .weather-meta-grid{grid-template-columns:repeat(2,1fr);margin-left:0;width:100%;} .agri-advice-grid{grid-template-columns:1fr;} }
</style>');
render_layout_open();
render_sidebar($user, 'weather');
?>
<div class="main-content">
<?php render_topbar($user, 'Hali ya Hewa'); ?>
<main class="page-content page-enter">

  <?php if (!empty($alerts)): ?>
  <div class="mb-6">
    <?php foreach ($alerts as $a): ?>
    <div class="alert <?= $a['type'] === 'warning' ? 'alert-gold' : ($a['type'] === 'danger' ? 'alert-red' : 'alert-sky') ?> mb-3">
      <span class="alert-icon"><?= $a['type'] === 'warning' ? '⚠️' : ($a['type'] === 'danger' ? '🚨' : 'ℹ️') ?></span>
      <div>
        <div class="alert-title"><?= esc($a['region_name']) ?> — <?= $a['type'] === 'warning' ? 'Tahadhari' : 'Taarifa' ?></div>
        <div class="alert-body"><?= esc($a['message']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="region-tabs" id="region-tabs"></div>
  <div class="weather-hero-card anim-fade-in-up" id="weather-hero">
    <div class="weather-main-icon" id="hero-icon">⛅</div>
    <div class="weather-main-info">
      <div class="weather-main-temp"><span id="hero-temp">29</span>°C</div>
      <div class="weather-main-label" id="hero-condition">Partly Cloudy</div>
      <div class="weather-main-region" id="hero-region-name">Dar es Salaam, Tanzania</div>
    </div>
    <div class="weather-meta-grid" id="weather-meta"></div>
  </div>

  <div class="mb-8">
    <div class="section-header">
      <div class="section-title">📅 Tabiri ya Siku 7</div>
      <span class="badge badge-sky badge-dot" id="forecast-region-label">Dar es Salaam</span>
    </div>
    <div class="forecast-7day stagger" id="forecast-7day"></div>
  </div>

  <div>
    <div class="section-header">
      <div class="section-title">🌾 Ushauri wa Kilimo kwa Hali ya Hewa Hii</div>
    </div>
    <div class="agri-advice-grid" id="agri-advice"></div>
  </div>

</main>
</div>
<?php render_layout_close(); ?>
<?php render_foot('<script src="' . base_path() . 'js/data.js"></script>
<script>
let currentRegion = dar;
const regionMap={"dar es salaam":"dar","morogoro":"morogoro","iringa":"iringa","dodoma":"dodoma","arusha":"arusha","mbeya":"mbeya","mwanza":"mwanza","tanga":"tanga"};
currentRegion = regionMap[currentRegion] || "dar";

const AGRI_TIPS = {
  sunny: [
    { icon:"☀️", bg:"rgba(232,184,48,.18)", title:"Mazao Yanayopenda Jua", body:"Hali nzuri ya kupanda mahindi, mtama, na karanga. Hakikisha udongo una unyevu wa kutosha." },
    { icon:"💧", bg:"rgba(52,152,219,.18)", title:"Umwagiliaji", body:"Hali kavu — ongeza umwagiliaji asubuhi mapema au jioni ili kupunguza uvukizi." },
    { icon:"🌱", bg:"rgba(58,143,74,.18)",  title:"Ukuaji Bora", body:"Joto la 24–30°C linafaa kwa ukuaji wa mazao mengi. Fuatilia mimea yako kila siku." },
  ],
  rainy: [
    { icon:"🌧️", bg:"rgba(52,152,219,.22)", title:"Tahadhari: Mvua Nyingi", body:"Epuka kupanda kwenye mashamba yenye mafuriko. Hifadhi mazao yaliyovunwa mahali pakavu." },
    { icon:"🍄", bg:"rgba(155,89,182,.18)", title:"Magonjwa ya Mimea", body:"Mvua nyingi huongeza hatari ya ukungu na magonjwa. Angalia mimea yako na tumia dawa za kuzuia." },
    { icon:"💦", bg:"rgba(58,143,74,.18)",  title:"Fursa ya Kuvuna Maji", body:"Weka mfumo wa kukusanya maji ya mvua kwa ajili ya umwagiliaji wa baadaye." },
  ],
  windy: [
    { icon:"💨", bg:"rgba(106,155,112,.18)", title:"Hatari ya Upepo", body:"Mimea mirefu kama mahindi inaweza kuharibika. Fanya msaada wa miking\'a ambapo inahitajika." },
    { icon:"🌡️", bg:"rgba(231,76,60,.15)",  title:"Upotevu wa Maji", body:"Upepo hukauka udongo haraka. Funika udongo na nyasi au mulch kupunguza uvukizi." },
    { icon:"🌻", bg:"rgba(232,184,48,.18)", title:"Wakati Mzuri wa Kupuliza", body:"Hali nzuri kwa kupuliza dawa za wadudu — dawa itaenea vizuri." },
  ],
};

function buildTabs() {
  const regions = [
    {id:"dar",name:"🌊 Dar es Salaam"},{id:"morogoro",name:"🏔️ Morogoro"},
    {id:"iringa",name:"⛰️ Iringa"},{id:"arusha",name:"🦁 Arusha"},
    {id:"mbeya",name:"🏔️ Mbeya"},{id:"mwanza",name:"🐟 Mwanza"},
    {id:"dodoma",name:"🌾 Dodoma"},{id:"tanga",name:"🌴 Tanga"},
  ];
  document.getElementById("region-tabs").innerHTML = regions.map(r =>
    `<button class="region-tab-btn ${r.id===currentRegion?"active":""}" id="rtab-${r.id}" onclick="selectRegion(\'${r.id}\')">${r.name}</button>`
  ).join("");
}

function selectRegion(id) {
  currentRegion = id;
  document.querySelectorAll(".region-tab-btn").forEach(b => b.classList.remove("active"));
  document.getElementById("rtab-"+id)?.classList.add("active");
  renderWeather();
}

function renderWeather() {
  const w = WEATHER_DATA[currentRegion];
  if (!w) return;
  document.getElementById("hero-icon").textContent = w.current.icon;
  document.getElementById("hero-temp").textContent = w.current.temp;
  document.getElementById("hero-condition").textContent = w.current.condition;
  document.getElementById("hero-region-name").textContent = w.region + ", Tanzania 🇹🇿";
  document.getElementById("forecast-region-label").textContent = w.region;
  document.getElementById("weather-meta").innerHTML = `
    <div class="weather-meta-item"><div class="meta-icon">💧</div><div class="meta-value">${w.current.humidity}%</div><div class="meta-label">Unyevu</div></div>
    <div class="weather-meta-item"><div class="meta-icon">💨</div><div class="meta-value">${w.current.wind} km/h</div><div class="meta-label">Upepo</div></div>
    <div class="weather-meta-item"><div class="meta-icon">🌡️</div><div class="meta-value">${w.current.feels}°C</div><div class="meta-label">Hisi Kama</div></div>
    <div class="weather-meta-item"><div class="meta-icon">🌧️</div><div class="meta-value">${w.forecast[0].rain}%</div><div class="meta-label">Mvua Leo</div></div>`;
  document.getElementById("forecast-7day").innerHTML = w.forecast.map((f,i) => `
    <div class="forecast-card ${i===0?"today":""}">
      <div class="fc-day">${i===0?"LEO":f.day.toUpperCase().slice(0,6)}</div>
      <div class="fc-icon">${f.icon}</div>
      <div class="fc-high">${f.high}°</div>
      <div class="fc-low">${f.low}° Chini</div>
      <div class="fc-rain">💧 ${f.rain}%<div class="fc-rain-bar"><div class="fc-rain-fill" style="width:${f.rain}%"></div></div></div>
    </div>`).join("");
  const cond = w.current.condition.toLowerCase();
  const tips = cond.includes("rain")||cond.includes("storm") ? AGRI_TIPS.rainy : cond.includes("wind") ? AGRI_TIPS.windy : AGRI_TIPS.sunny;
  document.getElementById("agri-advice").innerHTML = tips.map(t => `
    <div class="agri-advice-card">
      <div class="advice-icon-wrap" style="background:${t.bg}">${t.icon}</div>
      <h3 class="advice-title">${t.title}</h3>
      <p class="advice-body">${t.body}</p>
    </div>`).join("");
}

buildTabs(); renderWeather();

// Sidebar toggle
document.querySelector(".topbar-menu-btn")?.addEventListener("click", () => {
  document.querySelector(".sidebar").classList.toggle("collapsed");
  document.querySelector(".main-content").classList.toggle("expanded");
});
</script>'); ?>
