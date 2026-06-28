<?php
// ============================================================
// includes/layout.php — Shared HTML Partials
// ============================================================
require_once __DIR__ . '/helpers.php';

function render_head(string $title, string $extra_css = ''): void { ?>
<!doctype html>
<html lang="sw">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= esc($title) ?> | Kilimo Smart</title>
  <link rel="stylesheet" href="<?= base_path() ?>css/main.css"/>
  <link rel="stylesheet" href="<?= base_path() ?>css/animations.css"/>
  <?= $extra_css ?>
</head>
<body>
<?php }

function render_sidebar(array $user, string $active = ''): void {
    $is_admin = $user['role'] === 'admin';
    $nav = [
        ['href' => 'dashboard.php', 'icon' => '🏠', 'label' => 'Dashibodi',       'key' => 'dashboard'],
        ['href' => 'market.php',    'icon' => '📊', 'label' => 'Bei za Mazao',    'key' => 'market'],
        ['href' => 'weather.php',   'icon' => '⛅', 'label' => 'Hali ya Hewa',   'key' => 'weather'],
        ['href' => 'storage.php',   'icon' => '🏪', 'label' => 'Hifadhi',         'key' => 'storage'],
        ['href' => 'loans.php',     'icon' => '💳', 'label' => 'Mikopo',           'key' => 'loans'],
    ];
    if ($is_admin) {
        $nav[] = ['href' => 'admin.php', 'icon' => '⚙️', 'label' => 'Msimamizi', 'key' => 'admin'];
    }
?>
<aside class="sidebar">
  <div class="sidebar-header">
    <img src="<?=base_path()?>images/logo.png" alt="Kilimo Smart" height:="44" width:="44" style="border-radius:50%;border:2px solid var(--clr-green-400);padding:2px;background:white;object-fit:cover;flex: shrink 0;">
    <div>
      <div class="sidebar-brand">Kilimo Smart</div>
      <div class="sidebar-tagline">Farm Smart, Sell in Your Hand</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <?php foreach ($nav as $item): ?>
    <a href="<?= base_path() . esc($item['href']) ?>"
       class="nav-item <?= $active === $item['key'] ? 'active' : '' ?>">
      <span class="nav-icon"><?= $item['icon'] ?></span>
      <span><?= esc($item['label']) ?></span>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info-sidebar">
      <div class="user-avatar-sidebar"><?= esc($user['avatar'] ?? '🧑‍🌾') ?></div>
      <div>
        <div class="user-name-sidebar"><?= esc($user['name']) ?></div>
        <div class="user-role-sidebar"><?= esc($user['title'] ?? ($user['role'] === 'admin' ? 'Msimamizi' : 'Mkulima')) ?></div>
      </div>
    </div>
    <a href="<?= base_path() ?>api/logout.php" class="logout-btn" title="Toka">↩</a>
  </div>
</aside>
<?php }

function render_topbar(array $user, string $page_title = ''): void { ?>
<header class="topbar">
  <button class="topbar-menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
  <div class="topbar-title"><?= esc($page_title) ?></div>
  <div class="topbar-right">
    <?php $unread = unread_notifications_count($user['id']); ?>
    <a href="<?= base_path() ?>notifications.php" class="topbar-icon-btn" title="Arifa">
      🔔 <?php if ($unread > 0): ?><span class="notif-badge"><?= $unread ?></span><?php endif; ?>
    </a>
    <div class="topbar-user">
      <span class="topbar-avatar"><?= esc($user['avatar'] ?? '🧑‍🌾') ?></span>
      <span class="topbar-name"><?= esc($user['name']) ?></span>
    </div>
  </div>
</header>
<?php }

function render_layout_open(): void { ?>
<div class="app-shell">
<?php }

function render_layout_main_open(): void { ?>
<main class="main-content">
<?php }

function render_layout_close(): void { ?>
</main></div>
<?php }

function render_foot(string $extra_js = ''): void { ?>
<?= $extra_js ?>
<script>
// Sidebar toggle — works on all screen sizes
(function(){
  const btn = document.querySelector('.topbar-menu-btn');
  if (!btn) return;
  btn.addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-wrap').classList.toggle('expanded');
  });
})();
</script>
</body>
</html>
<?php }

function base_path(): string {
    return '/';
}

function unread_notifications_count(int $user_id): int {
    try {
        $row = db_row('SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0', [$user_id]);
        return (int)($row['cnt'] ?? 0);
    } catch (Throwable) { return 0; }
}
