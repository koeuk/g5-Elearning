<?php
/**
 * Shared student top navigation. Expects (all optional):
 *   $student   array  logged-in user (name, profile_image, email)
 *   $cartCount int    items in cart (shows a badge when > 0)
 *   $active    string current section key ('home' | 'cart' | 'profile')
 */
$student   = $student   ?? [];
$cartCount = $cartCount ?? 0;
$active    = $active    ?? '';
$sName     = $student['name'] ?? 'Student';
$sImg      = $student['profile_image'] ?? '';
$initial   = strtoupper(substr((string) $sName, 0, 1)) ?: 'S';
?>
<header class="snav">
  <div class="snav__wrap">
    <a class="snav__brand" href="/student"><span class="snav__dot"><i class="bi bi-mortarboard-fill"></i></span> E‑Learning</a>
    <nav class="snav__links">
      <a href="/student" style="<?= $active === 'home' ? 'color:var(--text);background:var(--surface-2)' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Home</a>
      <a href="/my_courses" style="<?= $active === 'courses' ? 'color:var(--text);background:var(--surface-2)' : '' ?>"><i class="bi bi-journal-bookmark-fill"></i> My Courses</a>
      <a href="/orders" style="<?= $active === 'cart' ? 'color:var(--text);background:var(--surface-2)' : '' ?>">
        <i class="bi bi-bag-heart-fill"></i> My cart
        <?php if ((int) $cartCount > 0): ?><span class="snav__badge"><?= (int) $cartCount ?></span><?php endif; ?>
      </a>
    </nav>
    <div class="snav__actions">
      <button class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode">
        <i class="bi bi-sun-fill ico-sun"></i><i class="bi bi-moon-stars-fill ico-moon"></i>
      </button>
      <div class="snav__profile">
        <button class="snav__avatar" data-menu-toggle="profileMenu" aria-haspopup="true" aria-expanded="false">
          <?php if ($sImg !== ''): ?>
            <img src="uploading/<?= e($sImg) ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'snav__ini',textContent:'<?= e($initial) ?>'}))">
          <?php else: ?>
            <span class="snav__ini"><?= e($initial) ?></span>
          <?php endif; ?>
          <span class="d-none-sm"><?= e($sName) ?></span>
          <i class="bi bi-chevron-down" style="font-size:.75rem"></i>
        </button>
        <div class="snav__menu" id="profileMenu" role="menu">
          <a href="/student_profile"><i class="bi bi-person"></i> My profile</a>
          <a href="/student_password"><i class="bi bi-shield-lock"></i> Change password</a>
          <hr>
          <a href="/logout" class="danger"><i class="bi bi-box-arrow-right"></i> Sign out</a>
        </div>
      </div>
    </div>
  </div>
</header>
