<?php date_default_timezone_set("Africa/Dar_es_Salaam"); ?>
<!doctype html>
<html lang="sw">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Kuhusu Kilimo Smart — Dhamira, maono, timu na bodi ya wakurugenzi."
    />
    <title>Kuhusu Sisi | Kilimo Smart</title>
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="css/animations.css" />
    <style>
      /* ── HERO ───────────────────────────────────── */
      .about-hero {
        background: var(--gradient-hero);
        padding: var(--space-16) var(--space-16) var(--space-12);
        text-align: center;
        position: relative;
        overflow: hidden;
      }
      .about-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(
          ellipse 60% 60% at 50% 50%,
          rgba(39, 105, 52, 0.2) 0%,
          transparent 70%
        );
      }
      .about-hero-content {
        position: relative;
        z-index: 2;
        max-width: 720px;
        margin: 0 auto;
      }
      .about-hero-icon {
        width: 80px;
        height: 80px;
        background: var(--gradient-green);
        border-radius: var(--radius-xl);
        display: grid;
        place-items: center;
        font-size: 2.5rem;
        margin: 0 auto var(--space-6);
        box-shadow: var(--shadow-glow-green);
        animation: floatUp 4s ease-in-out infinite;
      }

      /* ── MV CARDS ───────────────────────────────── */
      .mv-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-6);
        margin-bottom: var(--space-12);
      }
      .mv-card {
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: var(--glass-border);
        border-radius: var(--radius-xl);
        padding: var(--space-8);
      }
      .mv-card.vision { border-left: 4px solid var(--clr-green-400); }
      .mv-card.mission { border-left: 4px solid var(--clr-gold-400); }
      .mv-card.motto  { border-left: 4px solid var(--clr-sky-400); grid-column: 1 / -1; text-align: center; }
      .mv-icon {
        font-size: 2rem;
        margin-bottom: var(--space-4);
      }
      .mv-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--clr-text-muted);
        margin-bottom: var(--space-2);
      }
      .mv-text {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--clr-text-secondary);
      }
      .mv-motto-text {
        font-family: var(--font-display);
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #52b564 0%, #e8b830 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      /* ── VALUES ─────────────────────────────────── */
      .values-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-4);
        margin-bottom: var(--space-12);
      }
      .value-card {
        background: var(--clr-surface-1);
        border: var(--glass-border);
        border-radius: var(--radius-lg);
        padding: var(--space-6) var(--space-4);
        text-align: center;
        transition: all var(--tr-base);
      }
      .value-card:hover {
        background: var(--clr-surface-2);
        transform: translateY(-4px);
        border-color: var(--clr-border-bright);
      }
      .value-emoji { font-size: 2rem; margin-bottom: var(--space-3); }
      .value-name {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--clr-green-200);
        margin-bottom: var(--space-2);
      }
      .value-desc { font-size: 0.78rem; color: var(--clr-text-muted); line-height: 1.5; }

      /* ── TEAM ───────────────────────────────────── */
      .team-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-5);
        margin-bottom: var(--space-12);
      }
      .team-card {
        background: var(--glass-bg);
        backdrop-filter: var(--glass-blur);
        -webkit-backdrop-filter: var(--glass-blur);
        border: var(--glass-border);
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        text-align: center;
        transition: all var(--tr-slow);
      }
      .team-card:hover {
        transform: translateY(-6px);
        border-color: var(--clr-border-bright);
        box-shadow: var(--glass-shadow), var(--shadow-glow-green);
      }
      .team-avatar {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-full);
        background: var(--gradient-green);
        display: grid;
        place-items: center;
        font-size: 1.8rem;
        margin: 0 auto var(--space-4);
        box-shadow: var(--shadow-glow-green);
      }
      .team-name {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: var(--space-1);
      }
      .team-title {
        font-size: 0.75rem;
        color: var(--clr-text-muted);
        margin-bottom: var(--space-3);
      }
      .team-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: var(--radius-full);
        font-size: 0.68rem;
        font-weight: 700;
      }

      /* ── BOARD ──────────────────────────────────── */
      .board-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-4);
        margin-bottom: var(--space-12);
      }
      .board-card {
        background: var(--clr-surface-1);
        border: var(--glass-border);
        border-radius: var(--radius-lg);
        padding: var(--space-5) var(--space-4);
        text-align: center;
        transition: all var(--tr-base);
      }
      .board-card:hover {
        background: var(--clr-surface-2);
        transform: translateY(-3px);
      }
      .board-avatar {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, #b8860b 0%, #e8b830 100%);
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        margin: 0 auto var(--space-3);
      }
      .board-name { font-size: 0.88rem; font-weight: 700; margin-bottom: var(--space-1); }
      .board-role { font-size: 0.72rem; color: var(--clr-text-muted); }

      /* ── SECTION WRAPPER ────────────────────────── */
      .about-section {
        padding: var(--space-12) var(--space-16);
      }
      .about-section.alt {
        background: var(--clr-bg-secondary);
      }

      /* ── RESPONSIVE ─────────────────────────────── */
      @media (max-width: 1200px) {
        .team-grid { grid-template-columns: repeat(3, 1fr); }
        .values-grid { grid-template-columns: repeat(3, 1fr); }
        .board-grid { grid-template-columns: repeat(3, 1fr); }
      }
      @media (max-width: 960px) {
        .about-hero { padding: var(--space-12) var(--space-8); }
        .about-section { padding: var(--space-10) var(--space-6); }
        .mv-grid { grid-template-columns: 1fr; }
        .mv-card.motto { grid-column: 1; }
        .team-grid { grid-template-columns: repeat(2, 1fr); }
        .board-grid { grid-template-columns: repeat(2, 1fr); }
        .values-grid { grid-template-columns: repeat(2, 1fr); }
      }
      @media (max-width: 640px) {
        .about-hero { padding: var(--space-10) var(--space-4); }
        .about-section { padding: var(--space-8) var(--space-4); }
        .team-grid { grid-template-columns: 1fr 1fr; }
        .values-grid { grid-template-columns: 1fr 1fr; }
        .board-grid { grid-template-columns: 1fr 1fr; }
      }
    </style>
  </head>
  <body>

    <!-- NAVBAR -->
    <nav style="
      position:fixed; top:0; left:0; right:0;
      padding: var(--space-4) var(--space-8);
      display:flex; align-items:center; justify-content:space-between;
      z-index:200;
      background: rgba(10,31,14,0.85);
      -webkit-backdrop-filter: blur(12px);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(82,181,100,0.12);
    ">
      <a href="index.php" style="display:flex; align-items:center; gap:var(--space-3); text-decoration:none;">
        <div style="width:34px; height:34px; background:var(--gradient-green); border-radius:var(--radius-md); display:grid; place-items:center; font-size:1.1rem;">🌿</div>
        <span style="font-family:var(--font-display); font-size:1.1rem; font-weight:800; color:var(--clr-text-primary);">Kilimo Smart</span>
      </a>
      <div style="display:flex; gap:var(--space-8);">
        <a href="index.php" style="font-size:0.88rem; font-weight:500; color:var(--clr-text-secondary);">Nyumbani</a>
        <a href="index.php#features" style="font-size:0.88rem; font-weight:500; color:var(--clr-text-secondary);">Huduma</a>
        <a href="about.php" style="font-size:0.88rem; font-weight:500; color:var(--clr-text-primary);">Kuhusu Sisi</a>
      </div>
      <a href="index.php#auth" class="btn btn-primary btn-sm">Ingia Sasa</a>
    </nav>

    <!-- HERO -->
    <section class="about-hero" style="padding-top:120px;">
      <div class="about-hero-content anim-fade-in-up">
        <div class="about-hero-icon">🌿</div>
        <span class="section-eyebrow">Kuhusu Kilimo Smart</span>
        <h1 style="font-family:var(--font-display); font-size:clamp(2rem,4vw,3rem); font-weight:800; margin: var(--space-4) 0 var(--space-6);">
          Teknolojia Inayobadilisha<br/>
          <span style="background:linear-gradient(135deg,#52b564 0%,#e8b830 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">Kilimo cha Tanzania</span>
        </h1>
        <p style="font-size:1.05rem; color:var(--clr-text-secondary); max-width:560px; margin:0 auto; line-height:1.7;">
          Kilimo Smart ni jukwaa la dijitali linalounganisha wakulima wadogo wa Tanzania na masoko, hali ya hewa, hifadhi, na mikopo — kupitia simu yoyote.
        </p>
      </div>
    </section>

    <!-- MISSION / VISION / MOTTO -->
    <section class="about-section">
      <div style="text-align:center; margin-bottom:var(--space-10);">
        <span class="section-eyebrow">Msingi Wetu</span>
        <h2 style="font-family:var(--font-display); font-size:clamp(1.6rem,3vw,2.2rem); font-weight:700; margin-top:var(--space-3);">Dhamira, Maono na Kauli Mbiu</h2>
      </div>
      <div class="mv-grid">
        <div class="mv-card vision anim-fade-in-up">
          <div class="mv-icon">🌟</div>
          <div class="mv-label">Maono (Vision)</div>
          <p class="mv-text">
            Kuhakikisha kila mkulima Tanzania anakuwa mkulima hodari anayejua bei za soko, anapata masoko ya kuaminika, na analima kwa faida zaidi.
          </p>
        </div>
        <div class="mv-card mission anim-fade-in-up">
          <div class="mv-icon">🎯</div>
          <div class="mv-label">Dhamira (Mission)</div>
          <p class="mv-text">
            Kutoa teknolojia rahisi na ya bei nafuu kwa wakulima wadogo, kuwasiliana nao kwenye masoko ya kuaminika, na kubadilisha kilimo kuwa biashara yenye tija na heshima.
          </p>
        </div>
        <div class="mv-card motto anim-fade-in-up">
          <div class="mv-icon">✨</div>
          <div class="mv-label">Kauli Mbiu (Motto)</div>
          <div class="mv-motto-text">Farm Smart, Sell in Your Hand.</div>
        </div>
      </div>
    </section>

    <!-- CORE VALUES -->
    <section class="about-section alt">
      <div style="text-align:center; margin-bottom:var(--space-10);">
        <span class="section-eyebrow">Maadili Yetu</span>
        <h2 style="font-family:var(--font-display); font-size:clamp(1.6rem,3vw,2.2rem); font-weight:700; margin-top:var(--space-3);">Maadili Makuu 5 ya Kilimo Smart</h2>
      </div>
      <div class="values-grid stagger">
        <div class="value-card anim-fade-in-up">
          <div class="value-emoji">🤝</div>
          <div class="value-name">Urahisi</div>
          <div class="value-desc">Rahisi kutumia kwa wakulima wa ngazi zote za elimu na teknolojia.</div>
        </div>
        <div class="value-card anim-fade-in-up">
          <div class="value-emoji">🔍</div>
          <div class="value-name">Uwazi</div>
          <div class="value-desc">Taarifa za soko wazi, za kweli, na zinazoweza kuaminika.</div>
        </div>
        <div class="value-card anim-fade-in-up">
          <div class="value-emoji">💪</div>
          <div class="value-name">Ustahimilivu</div>
          <div class="value-desc">Imejengwa kustahimili changamoto na kukua kwa njia endelevu.</div>
        </div>
        <div class="value-card anim-fade-in-up">
          <div class="value-emoji">⚖️</div>
          <div class="value-name">Usawa</div>
          <div class="value-desc">Inapatikana kwa wote, hasa wanawake na vijana wanaofanya kilimo.</div>
        </div>
        <div class="value-card anim-fade-in-up">
          <div class="value-emoji">💡</div>
          <div class="value-name">Ubunifu</div>
          <div class="value-desc">Inaboresha daima kupitia teknolojia ya kisasa na maoni ya wakulima.</div>
        </div>
      </div>
    </section>

    <!-- BOARD OF DIRECTORS -->
    <section class="about-section">
      <div style="text-align:center; margin-bottom:var(--space-10);">
        <span class="section-eyebrow">Uongozi wa Juu</span>
        <h2 style="font-family:var(--font-display); font-size:clamp(1.6rem,3vw,2.2rem); font-weight:700; margin-top:var(--space-3);">Bodi ya Wakurugenzi</h2>
        <p style="color:var(--clr-text-muted); margin-top:var(--space-3); font-size:0.9rem;">Wataalam wanaosimamia mwelekeo wa mkakati wa Kilimo Smart</p>
      </div>
      <div class="board-grid stagger">
        <div class="board-card anim-fade-in-up">
          <div class="board-avatar">🌿</div>
          <div class="board-name">Karol Vicent</div>
          <div class="board-role">Mtaalamu wa Teknolojia ya Kilimo</div>
        </div>
        <div class="board-card anim-fade-in-up">
          <div class="board-avatar">👩‍🌾</div>
          <div class="board-name">Enimelda Raphael</div>
          <div class="board-role">Mwakilishi wa Wakulima</div>
        </div>
        <div class="board-card anim-fade-in-up">
          <div class="board-avatar">⚖️</div>
          <div class="board-name">Maliki Moshi Luwungo</div>
          <div class="board-role">Mshauri wa Kisheria (Wakili)</div>
        </div>
        <div class="board-card anim-fade-in-up">
          <div class="board-avatar">📊</div>
          <div class="board-name">Priscar Laurence Mrope</div>
          <div class="board-role">Mtaalamu wa Biashara</div>
        </div>
        <div class="board-card anim-fade-in-up">
          <div class="board-avatar">💰</div>
          <div class="board-name">Maulida Nuru Hoseni</div>
          <div class="board-role">Mtaalamu wa Fedha</div>
        </div>
      </div>
    </section>

    <!-- MANAGEMENT TEAM -->
    <section class="about-section alt">
      <div style="text-align:center; margin-bottom:var(--space-10);">
        <span class="section-eyebrow">Timu ya Utekelezaji</span>
        <h2 style="font-family:var(--font-display); font-size:clamp(1.6rem,3vw,2.2rem); font-weight:700; margin-top:var(--space-3);">Timu ya Usimamizi na Uendeshaji</h2>
        <p style="color:var(--clr-text-muted); margin-top:var(--space-3); font-size:0.9rem;">Watu wanaofanya ndoto ya Kilimo Smart kuwa ukweli kila siku</p>
      </div>
      <div class="team-grid stagger">
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">👨‍💼</div>
          <div class="team-name">Yohana Samwel Machuma</div>
          <div class="team-title">Mkurugenzi Mtendaji (CEO)</div>
          <span class="badge badge-green team-role-badge">Uongozi</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">👨‍💻</div>
          <div class="team-name">William Patrick Msafiri</div>
          <div class="team-title">Mkurugenzi wa Teknolojia (CTO)</div>
          <span class="badge badge-sky team-role-badge">Teknolojia</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">💰</div>
          <div class="team-name">Maulida Nuru Hoseni</div>
          <div class="team-title">Meneja wa Fedha na Uwekezaji</div>
          <span class="badge badge-gold team-role-badge">Fedha</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">📊</div>
          <div class="team-name">Priscar Laurence Mrope</div>
          <div class="team-title">Mhasibu na Mkaguzi wa Fedha</div>
          <span class="badge badge-gold team-role-badge">Fedha</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">🚜</div>
          <div class="team-name">Saidi Bahati Ally</div>
          <div class="team-title">Meneja wa Shughuli za Uwanjani</div>
          <span class="badge badge-green team-role-badge">Uendeshaji</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">👩‍🌾</div>
          <div class="team-name">Enimelda Raphael</div>
          <div class="team-title">Afisa wa Ugani wa Dijitali</div>
          <span class="badge badge-green team-role-badge">Maarifa</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">👥</div>
          <div class="team-name">Amani Amon</div>
          <div class="team-title">Mratibu wa Rasilimali Watu na Mafunzo</div>
          <span class="badge badge-muted team-role-badge">Watu</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">📣</div>
          <div class="team-name">Coletha Deodatus Lwala</div>
          <div class="team-title">Afisa wa Masoko na Usafirishaji</div>
          <span class="badge badge-orange team-role-badge">Masoko</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">⚖️</div>
          <div class="team-name">Maliki Moshi Luwungo</div>
          <div class="team-title">Afisa wa Ufuatiliaji na Tathmini</div>
          <span class="badge badge-muted team-role-badge">M&amp;E</span>
        </div>
        <div class="team-card anim-fade-in-up">
          <div class="team-avatar">🤝</div>
          <div class="team-name">Mwaija Halfani Mnyachi</div>
          <div class="team-title">Balozi wa Jamii na Ushirikiano</div>
          <span class="badge badge-sky team-role-badge">Ushirikiano</span>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer style="background:var(--clr-bg-primary); border-top:var(--glass-border); padding:var(--space-8) var(--space-16); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-4);">
      <div style="font-family:var(--font-display); font-weight:800; font-size:1rem;">🌿 Kilimo Smart</div>
      <div style="font-size:0.78rem; color:var(--clr-text-muted);">© <?= date("Y") ?> Kilimo Smart Tanzania Ltd. Haki zote zimehifadhiwa.</div>
      <a href="index.php" class="btn btn-outline btn-sm">← Rudi Nyumbani</a>
    </footer>

  </body>
</html>
