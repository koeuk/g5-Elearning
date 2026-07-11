<?php
/**
 * Student profile. Standalone page in the shared student design system.
 *
 * @var array $student profile subject (name, email, phone, profile_image)
 * @var array $courses purchased courses (course_id, title, description, image_courses)
 * @var bool  $isAdmin whether an admin is viewing
 */
use App\Core\View;

$student = $student ?? [];
$courses = $courses ?? [];
$isAdmin = $isAdmin ?? false;
$email   = $student['email'] ?? '';
$name    = $student['name'] ?? 'Student';
$img     = $student['profile_image'] ?? '';
$initial = strtoupper(substr((string) $name, 0, 1)) ?: 'S';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My profile — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>
    .d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    .prof{display:grid;grid-template-columns:320px 1fr;gap:1.8rem;align-items:start}
    @media(max-width:820px){.prof{grid-template-columns:1fr}}
    .prof-card{background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:2rem 1.6rem;text-align:center;}
    .prof-ava{width:120px;height:120px;border-radius:50%;object-fit:cover;margin:0 auto .9rem;display:grid;place-items:center;
      background:var(--surface-3);color:var(--accent);font-family:var(--serif);font-size:3rem;border:3px solid var(--surface-2)}
    .prof-card h2{font-family:var(--serif);font-weight:600;font-size:1.5rem;margin:0}
    .prof-card .role{color:var(--accent);font-weight:700;font-size:.78rem;text-transform:uppercase;letter-spacing:.12em;margin-top:.2rem}
    .prof-rows{margin-top:1.4rem;text-align:left;display:flex;flex-direction:column;gap:.2rem}
    .prof-row{display:flex;align-items:center;gap:.8rem;padding:.75rem .3rem;border-top:1px solid var(--line)}
    .prof-row .ic{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;background:var(--bg-tint-1);color:var(--accent);flex:none}
    .prof-row small{color:var(--muted);font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;display:block}
    .prof-row span{font-weight:600;word-break:break-word}
    /* Edit-profile popup dialog */
    .prof-dialog{border:none;border-radius:20px;padding:0;width:92%;max-width:430px;background:var(--surface);color:var(--text);
      box-shadow:0 40px 100px -30px rgba(0,0,0,.55);overflow:hidden}
    .prof-dialog::backdrop{background:rgba(15,12,8,.5);backdrop-filter:blur(3px)}
    .prof-dialog[open]{animation:ui-fadeup .28s cubic-bezier(.2,.7,.2,1) both}
    .prof-dialog__head{display:flex;align-items:center;justify-content:space-between;padding:1.15rem 1.5rem;border-bottom:1px solid var(--line)}
    .prof-dialog__head h3{margin:0;font-family:var(--serif);font-weight:600;font-size:1.28rem;display:flex;align-items:center}
    .prof-dialog__x{background:transparent;border:none;color:var(--muted);font-size:1.7rem;line-height:1;cursor:pointer;padding:0 .2rem;transition:color .2s}
    .prof-dialog__x:hover{color:var(--accent)}
    .prof-dialog form{padding:1.4rem 1.5rem 1.6rem}
    .crs__desc{color:var(--muted);font-size:.88rem;margin:0 0 .9rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'active' => 'profile']) ?>

  <section class="page" style="padding:2.5rem 0">
    <div class="prof">
      <!-- Profile card -->
      <aside class="prof-card">
        <?php if ($img !== ''): ?>
          <img class="prof-ava" src="uploading/<?= e($img) ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'prof-ava',textContent:'<?= e($initial) ?>'}))">
        <?php else: ?>
          <div class="prof-ava"><?= e($initial) ?></div>
        <?php endif; ?>
        <h2><?= e($name) ?></h2>
        <div class="role"><?= $isAdmin ? 'Admin view' : 'Student' ?></div>

        <div class="prof-rows">
          <div class="prof-row"><span class="ic"><i class="bi bi-envelope"></i></span><span><small>Email</small><?= e($email) ?></span></div>
          <div class="prof-row"><span class="ic"><i class="bi bi-telephone"></i></span><span><small>Phone</small><?= e($student['phone'] ?? '—') ?></span></div>
          <div class="prof-row"><span class="ic"><i class="bi bi-gender-ambiguous"></i></span><span><small>Gender</small><?= e($student['gender'] ?? '—') ?></span></div>
        </div>

        <button class="ui-btn ui-btn--ghost ui-btn--block" id="editToggle" style="margin-top:1.3rem"><i class="bi bi-pencil-square"></i> Edit profile</button>

        <dialog class="prof-dialog" id="editDialog">
          <div class="prof-dialog__head">
            <h3><i class="bi bi-pencil-square" style="color:var(--accent);margin-right:.4rem"></i>Edit profile</h3>
            <button type="button" class="prof-dialog__x" id="editClose" aria-label="Close">&times;</button>
          </div>
          <form action="/get_edit" method="post" enctype="multipart/form-data">
            <?php if ($isAdmin && isset($student['user_id'])): ?><input type="hidden" name="id" value="<?= (int) $student['user_id'] ?>"><?php endif; ?>
            <div class="ui-field" style="text-align:left"><label class="ui-label" for="name">Name</label>
              <input class="ui-input" id="name" name="name" value="<?= e($name) ?>"></div>
            <div class="ui-field" style="text-align:left"><label class="ui-label" for="phone">Phone</label>
              <input class="ui-input" id="phone" name="phone" value="<?= e($student['phone'] ?? '') ?>"></div>
            <div class="ui-field" style="text-align:left"><label class="ui-label" for="email">Email</label>
              <input class="ui-input" id="email" name="email" value="<?= e($email) ?>"></div>
            <div class="ui-field" style="text-align:left"><label class="ui-label" for="image">Profile photo</label>
              <input class="ui-input" type="file" id="image" name="image" accept="image/*" style="padding:.6rem 1rem"></div>
            <div style="display:flex;gap:.6rem;margin-top:.3rem">
              <button class="ui-btn ui-btn--ghost" type="button" id="editCancel" style="flex:1">Cancel</button>
              <button class="ui-btn ui-btn--primary" type="submit" style="flex:2"><i class="bi bi-check2"></i> Save changes</button>
            </div>
          </form>
        </dialog>
      </aside>

      <!-- Purchased courses -->
      <div>
        <div class="section__head" style="text-align:left;margin:0 0 1.4rem">
          <p class="k">Your library</p><h2>My courses</h2>
        </div>
        <?php if (empty($courses)): ?>
          <div class="empty-block"><i class="bi bi-journal-bookmark"></i>You haven’t enrolled in any courses yet.
            <div style="margin-top:1rem"><a href="/student" class="ui-btn ui-btn--primary" style="padding:.6rem 1.2rem"><i class="bi bi-search"></i> Find courses</a></div>
          </div>
        <?php else: ?>
          <div class="course-grid">
            <?php foreach ($courses as $course): ?>
              <article class="crs">
                <div class="crs__media">
                  <img src="uploading/<?= e($course['image_courses']) ?>" alt="" onerror="this.style.display='none'">
                  <span class="crs__owned"><i class="bi bi-check-circle-fill"></i> Owned</span>
                </div>
                <div class="crs__body">
                  <h3 class="crs__title"><?= e($course['title']) ?></h3>
                  <p class="crs__desc"><?= e($course['description'] ?? '') ?></p>
                  <div class="crs__foot">
                    <span class="crs__enroll"><span class="ic"><i class="bi bi-mortarboard-fill"></i></span> Enrolled</span>
                    <form action="/blog_learning" method="post">
                      <input type="hidden" name="email" value="<?= e($email) ?>">
                      <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                      <button type="submit" class="crs__join"><i class="bi bi-play-fill"></i> Continue</button>
                    </form>
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
      <span>© <?= date('Y') ?> E‑Learning — grow your skills.</span>
      <div class="foot__soc">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
      </div>
    </div>
  </footer>

  <script src="assets/theme.js"></script>
  <script>
    (function () {
      var dlg = document.getElementById('editDialog');
      if (!dlg) return;
      document.getElementById('editToggle').addEventListener('click', function () { dlg.showModal(); });
      document.getElementById('editClose').addEventListener('click', function () { dlg.close(); });
      document.getElementById('editCancel').addEventListener('click', function () { dlg.close(); });
      // Close when clicking the dark backdrop (outside the dialog box).
      dlg.addEventListener('click', function (e) {
        var r = dlg.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dlg.close();
      });
    })();
  </script>
</body>
</html>
