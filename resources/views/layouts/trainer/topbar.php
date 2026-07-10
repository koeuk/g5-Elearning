<?php
/**
 * Shared trainer top navigation. Expects (optional):
 *   $trainer array  logged-in trainer (name, profile_image)
 *   $active  string current section key ('dashboard')
 */
$trainer = $trainer ?? [];
$active  = $active  ?? '';
$tName   = $trainer['name'] ?? 'Trainer';
$tImg    = $trainer['profile_image'] ?? '';
$initial = strtoupper(substr((string) $tName, 0, 1)) ?: 'T';
?>
<header class="snav">
  <div class="snav__wrap">
    <a class="snav__brand" href="/trainer">
      <span class="snav__dot"><i class="bi bi-easel2-fill"></i></span> E‑Learning
      <span style="font-size:.72rem;font-weight:700;color:var(--accent);border:1px solid var(--accent);border-radius:99px;padding:.05rem .5rem;margin-left:.2rem">TRAINER</span>
    </a>
    <nav class="snav__links">
      <a href="/trainer" style="<?= $active === 'dashboard' ? 'color:var(--text);background:var(--surface-2)' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
    </nav>
    <div class="snav__actions">
      <button class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode">
        <i class="bi bi-sun-fill ico-sun"></i><i class="bi bi-moon-stars-fill ico-moon"></i>
      </button>
      <div class="snav__profile">
        <button class="snav__avatar" data-menu-toggle="trProfileMenu" aria-haspopup="true" aria-expanded="false">
          <?php if ($tImg !== ''): ?>
            <img src="uploading/<?= e($tImg) ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'snav__ini',textContent:'<?= e($initial) ?>'}))">
          <?php else: ?>
            <span class="snav__ini"><?= e($initial) ?></span>
          <?php endif; ?>
          <span class="d-none-sm"><?= e($tName) ?></span>
          <i class="bi bi-chevron-down" style="font-size:.75rem"></i>
        </button>
        <div class="snav__menu" id="trProfileMenu" role="menu">
          <a href="/trainer"><i class="bi bi-speedometer2"></i> Dashboard</a>
          <hr>
          <a href="/trainer_logout" class="danger"><i class="bi bi-box-arrow-right"></i> Sign out</a>
        </div>
      </div>
    </div>
  </div>
</header>
