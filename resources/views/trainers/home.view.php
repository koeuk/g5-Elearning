<?php
/**
 * Trainer dashboard. Standalone page in the shared design system (light/dark).
 *
 * @var array $trainer                the logged-in trainer's user row
 * @var array<int, array> $myCourses  courses owned by this trainer
 * @var array<int, array> $allCourses every course
 * @var array<int, string> $trainerNames  user_id => trainer name (for All courses)
 */
use App\Core\View;

$trainer      = $trainer ?? [];
$myCourses    = $myCourses ?? [];
$allCourses   = $allCourses ?? [];
$trainerNames = $trainerNames ?? [];
$name         = $trainer['name'] ?? 'Trainer';
$img          = $trainer['profile_image'] ?? '';
$initial      = strtoupper(substr((string) $name, 0, 1)) ?: 'T';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trainer dashboard — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>
    .d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    .tr-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin:1.6rem 0 0}
    .tr-stat{background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:1.1rem 1.3rem;display:flex;align-items:center;gap:.9rem;box-shadow:var(--shadow)}
    .tr-stat .ic{width:46px;height:46px;border-radius:13px;flex:none;display:grid;place-items:center;background:var(--bg-tint-1);color:var(--accent);font-size:1.3rem}
    .tr-stat b{font-family:var(--serif);font-weight:700;font-size:1.5rem;display:block;line-height:1}
    .tr-stat span{color:var(--muted);font-size:.85rem}
    .tr-grid{display:grid;grid-template-columns:340px 1fr;gap:1.8rem;align-items:start}
    @media(max-width:900px){.tr-grid{grid-template-columns:1fr}}
    .prof-card{background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:2rem 1.6rem;text-align:center;box-shadow:var(--shadow)}
    .prof-ava{width:120px;height:120px;border-radius:50%;object-fit:cover;margin:0 auto .9rem;display:grid;place-items:center;background:var(--surface-3);color:var(--accent);font-family:var(--serif);font-size:3rem;border:3px solid var(--surface-2)}
    .prof-card h2{font-family:var(--serif);font-weight:600;font-size:1.5rem;margin:0}
    .prof-card .role{color:var(--accent);font-weight:700;font-size:.78rem;text-transform:uppercase;letter-spacing:.12em;margin-top:.2rem}
    .prof-rows{margin:1.4rem 0 0;text-align:left;display:flex;flex-direction:column}
    .prof-row{display:flex;align-items:center;gap:.8rem;padding:.75rem .3rem;border-top:1px solid var(--line)}
    .prof-row .ic{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;background:var(--bg-tint-1);color:var(--accent);flex:none}
    .prof-row small{color:var(--muted);font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;display:block}
    .prof-row span{font-weight:600;word-break:break-word}
    .toggle-form{display:none;margin-top:1rem;text-align:left}
    .toggle-form.is-open{display:block;animation:ui-fadeup .25s both}
    .crs__desc{color:var(--muted);font-size:.88rem;margin:0 0 .9rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
    .crs__manage{background:var(--accent);color:var(--accent-ink);border:none;border-radius:10px;padding:.5rem .9rem;font-weight:700;font-size:.85rem;cursor:pointer;font-family:var(--sans);text-decoration:none;display:inline-flex;align-items:center;gap:.35rem}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/trainer/topbar', ['trainer' => $trainer, 'active' => 'dashboard']) ?>

  <!-- Hero -->
  <section class="page hero-home" style="text-align:left;padding:2.6rem 0 1rem">
    <p class="k">Trainer studio</p>
    <h1 style="margin:0">Welcome back, <em><?= e($name) ?></em>.</h1>
    <p style="margin-left:0">Manage your courses, publish lessons and quizzes, and keep an eye on your learners.</p>
    <div class="tr-stats">
      <div class="tr-stat"><span class="ic"><i class="bi bi-journal-bookmark-fill"></i></span><span><b><?= count($myCourses) ?></b><span>Courses you teach</span></span></div>
      <div class="tr-stat"><span class="ic"><i class="bi bi-collection-fill"></i></span><span><b><?= count($allCourses) ?></b><span>Courses on the platform</span></span></div>
      <div class="tr-stat"><span class="ic"><i class="bi bi-patch-check-fill"></i></span><span><b>Active</b><span>Teaching status</span></span></div>
    </div>
  </section>

  <section class="page section" style="padding-top:1.6rem">
    <div class="tr-grid">
      <!-- Profile -->
      <aside class="prof-card">
        <?php if ($img !== ''): ?>
          <img class="prof-ava" src="uploading/<?= e($img) ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'prof-ava',textContent:'<?= e($initial) ?>'}))">
        <?php else: ?>
          <div class="prof-ava"><?= e($initial) ?></div>
        <?php endif; ?>
        <h2><?= e($name) ?></h2>
        <div class="role">Trainer</div>

        <div class="prof-rows">
          <div class="prof-row"><span class="ic"><i class="bi bi-envelope"></i></span><span><small>Email</small><?= e($trainer['email'] ?? '—') ?></span></div>
          <div class="prof-row"><span class="ic"><i class="bi bi-telephone"></i></span><span><small>Phone</small><?= e($trainer['phone'] ?? '—') ?></span></div>
          <div class="prof-row"><span class="ic"><i class="bi bi-mortarboard"></i></span><span><small>Courses</small><?= count($myCourses) ?> course<?= count($myCourses) === 1 ? '' : 's' ?></span></div>
        </div>

        <div style="display:flex;gap:.5rem;margin-top:1.3rem">
          <button class="ui-btn ui-btn--ghost" style="flex:1;padding:.6rem" id="btnEdit"><i class="bi bi-pencil-square"></i> Edit</button>
          <button class="ui-btn ui-btn--ghost" style="flex:1;padding:.6rem" id="btnPw"><i class="bi bi-shield-lock"></i> Password</button>
        </div>

        <!-- Edit profile -->
        <form class="toggle-form" id="editForm" action="/trainer_edits" method="post" enctype="multipart/form-data">
          <div class="ui-field"><label class="ui-label" for="name">Name</label><input class="ui-input" id="name" name="name" value="<?= e($name) ?>" required></div>
          <div class="ui-field"><label class="ui-label" for="phone">Phone</label><input class="ui-input" id="phone" name="phone" value="<?= e($trainer['phone'] ?? '') ?>"></div>
          <div class="ui-field"><label class="ui-label" for="email">Email</label><input class="ui-input" id="email" name="email" type="email" value="<?= e($trainer['email'] ?? '') ?>" required></div>
          <div class="ui-field"><label class="ui-label" for="image">Profile photo</label><input class="ui-input" type="file" id="image" name="image" accept="image/*" style="padding:.6rem 1rem"></div>
          <button class="ui-btn ui-btn--primary ui-btn--block" type="submit"><i class="bi bi-check2"></i> Save profile</button>
        </form>

        <!-- Change password -->
        <form class="toggle-form" id="pwForm" action="/trainer_password_comfirm" method="post">
          <div class="ui-field"><label class="ui-label" for="currentPassword">Current password</label><input class="ui-input" type="password" id="currentPassword" name="currentPassword" required></div>
          <div class="ui-field"><label class="ui-label" for="newPassword">New password</label><input class="ui-input" type="password" id="newPassword" name="newPassword" required></div>
          <div class="ui-field"><label class="ui-label" for="confirmPassword">Confirm password</label><input class="ui-input" type="password" id="confirmPassword" name="confirmPassword" required></div>
          <button class="ui-btn ui-btn--primary ui-btn--block" type="submit"><i class="bi bi-check2-circle"></i> Update password</button>
        </form>
      </aside>

      <!-- My courses -->
      <div>
        <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Teaching</p><h2>My courses</h2></div>
        <?php if (empty($myCourses)): ?>
          <div class="empty-block"><i class="bi bi-journal-plus"></i>You aren’t teaching any courses yet. An admin can assign courses to you.</div>
        <?php else: ?>
          <div class="course-grid">
            <?php foreach ($myCourses as $course): ?>
              <article class="crs">
                <div class="crs__media">
                  <img src="uploading/<?= e($course['image_courses'] ?? '') ?>" alt="" onerror="this.style.display='none'">
                </div>
                <div class="crs__body">
                  <h3 class="crs__title"><?= e($course['title'] ?? '') ?></h3>
                  <p class="crs__desc"><?= e($course['description'] ?? '') ?></p>
                  <div class="crs__foot">
                    <span class="crs__enroll"><span class="ic"><i class="bi bi-mortarboard-fill"></i></span> $<?= e($course['price'] ?? '0') ?></span>
                    <a class="crs__manage" href="/trainer_manage?course=<?= (int) $course['course_id'] ?>"><i class="bi bi-sliders"></i> Manage</a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- All courses -->
        <?php if (!empty($allCourses)): ?>
        <div class="section__head" style="text-align:left;margin:2.6rem 0 1.4rem"><p class="k">Catalogue</p><h2>All courses</h2></div>
        <div class="course-grid">
          <?php foreach ($allCourses as $course): ?>
            <article class="crs">
              <div class="crs__media">
                <img src="uploading/<?= e($course['image_courses'] ?? '') ?>" alt="" onerror="this.style.display='none'">
              </div>
              <div class="crs__body">
                <h3 class="crs__title"><?= e($course['title'] ?? '') ?></h3>
                <div class="crs__meta"><i class="bi bi-person-badge" style="color:var(--accent)"></i> <span><?= e($trainerNames[$course['user_id'] ?? 0] ?? 'Trainer') ?></span></div>
                <div class="crs__foot">
                  <span class="crs__enroll"><span class="ic"><i class="bi bi-tag-fill"></i></span> $<?= e($course['price'] ?? '0') ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <footer class="foot">
    <div class="foot__wrap">
      <span>© <?= date('Y') ?> E‑Learning — trainer studio.</span>
      <a href="/trainer_logout" class="ui-btn ui-btn--ghost" style="padding:.45rem .9rem;font-size:.85rem"><i class="bi bi-box-arrow-right"></i> Sign out</a>
    </div>
  </footer>

  <script src="assets/theme.js"></script>
  <script>
    (function(){
      function toggle(btn, form, other){
        btn.addEventListener('click', function(){
          if (other.classList.contains('is-open')) other.classList.remove('is-open');
          form.classList.toggle('is-open');
        });
      }
      var ef = document.getElementById('editForm'), pf = document.getElementById('pwForm');
      toggle(document.getElementById('btnEdit'), ef, pf);
      toggle(document.getElementById('btnPw'), pf, ef);
    })();
  </script>
</body>
</html>
