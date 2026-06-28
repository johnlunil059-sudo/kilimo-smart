<?php
// notifications.php — Arifa
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';
session_start_safe();
$user = require_auth();

// Mark all as read when page is opened
db_run('UPDATE notifications SET is_read = 1 WHERE user_id = ?', [$user['id']]);

// Load notifications
$notifications = db_rows(
    'SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
    [$user['id']]
);

render_head('Arifa', '<style>
.notif-list { display:flex; flex-direction:column; gap:var(--space-3); }
.notif-item { display:flex; align-items:flex-start; gap:var(--space-4); background:var(--glass-bg); border:var(--glass-border); border-radius:var(--radius-lg); padding:var(--space-5); transition:all var(--tr-base); }
.notif-item:hover { border-color:var(--clr-border-bright); background:var(--clr-surface-2); }
.notif-item.unread { border-left:3px solid var(--clr-green-400); background:var(--clr-surface-1); }
.notif-icon { width:44px; height:44px; border-radius:var(--radius-full); display:grid; place-items:center; font-size:1.3rem; flex-shrink:0; }
.notif-icon.price   { background:rgba(232,184,48,.18); }
.notif-icon.weather { background:rgba(52,152,219,.18); }
.notif-icon.loan    { background:rgba(58,143,74,.18); }
.notif-icon.storage { background:rgba(155,89,182,.18); }
.notif-icon.system  { background:rgba(106,155,112,.18); }
.notif-title { font-size:.95rem; font-weight:600; margin-bottom:var(--space-1); }
.notif-time  { font-size:.78rem; color:var(--clr-text-muted); }
.notif-unread-dot { width:8px; height:8px; background:var(--clr-green-400); border-radius:50%; margin-left:auto; flex-shrink:0; margin-top:6px; }
.empty-state { text-align:center; padding:var(--space-16); color:var(--clr-text-muted); }
.empty-state .empty-icon { font-size:4rem; margin-bottom:var(--space-4); }
</style>');
render_layout_open();
render_sidebar($user, '');
?>
<div class="main-content">
<?php render_topbar($user, 'Arifa'); ?>
<main class="page-content page-enter">

  <div class="section-header mb-6">
    <div class="section-title">🔔 Arifa Zako</div>
    <?php $total = count($notifications); ?>
    <span class="badge badge-green"><?= $total ?> arifa</span>
  </div>

  <?php if (empty($notifications)): ?>
  <div class="empty-state">
    <div class="empty-icon">🔔</div>
    <h3>Hakuna Arifa</h3>
    <p>Arifa zako zitaonekana hapa.</p>
  </div>
  <?php else: ?>

  <div class="notif-list">
    <?php
    $icons = [
      'price'   => ['icon' => '📊', 'class' => 'price'],
      'weather' => ['icon' => '⛅', 'class' => 'weather'],
      'loan'    => ['icon' => '💳', 'class' => 'loan'],
      'storage' => ['icon' => '🏪', 'class' => 'storage'],
      'system'  => ['icon' => '⚙️', 'class' => 'system'],
    ];
    foreach ($notifications as $n):
      $type = $n['type'] ?? 'system';
      $ic = $icons[$type] ?? $icons['system'];
    ?>
    <div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>">
      <div class="notif-icon <?= $ic['class'] ?>"><?= $ic['icon'] ?></div>
      <div style="flex:1">
        <div class="notif-title"><?= esc($n['title']) ?></div>
        <?php if (!empty($n['body'])): ?>
        <div class="notif-time" style="margin-bottom:var(--space-1)"><?= esc($n['body']) ?></div>
        <?php endif; ?>
        <div class="notif-time"><?= time_ago($n['created_at']) ?></div>
      </div>
      <?php if (!$n['is_read']): ?>
      <div class="notif-unread-dot"></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>

</main>
</div>
<?php render_layout_close(); ?>
<?php render_foot('<script>
document.querySelector(".topbar-menu-btn")?.addEventListener("click", () => {
  document.querySelector(".sidebar").classList.toggle("collapsed");
  document.querySelector(".main-content").classList.toggle("expanded");
});
</script>'); ?>
