<?php
/**
 * Student course list for one category. Standalone page in the shared student
 * design system (light/dark).
 *
 * @var array $student    logged-in student (email, user_id …)
 * @var array $category   the chosen category (title …)
 * @var int   $categoryId the chosen category id
 * @var array $courses    courses in the category, each with paid/in_cart flags
 */
use App\Core\View;

$student    = $student ?? [];
$category   = $category ?? [];
$categoryId = $categoryId ?? 0;
$courses    = $courses ?? [];
$email      = $student['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($category['title'] ?? 'Category') ?> — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>.d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    .crs__desc{color:var(--muted);font-size:.88rem;margin:0 0 .9rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
    /* Category top bar: back (left) + filter (right) */
    .cat-topbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.8rem;text-align:left}
    .cat-filter{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
    .cat-filter .ui-input{padding-top:.62rem;padding-bottom:.62rem;font-size:.9rem}
    .cat-filter .ui-inputwrap{min-width:210px}
    .cat-select{width:auto;min-width:158px;cursor:pointer}
    @media(max-width:560px){.cat-topbar{flex-direction:column;align-items:stretch}.cat-filter{width:100%}.cat-filter .ui-inputwrap{flex:1}}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'active' => 'home']) ?>

  <!-- Category hero -->
  <section class="page hero-home" style="padding-top:2.5rem">
    <!-- Top bar: Back (left) + course filter (right) -->
    <div class="cat-topbar">
      <a href="/student" class="ui-btn ui-btn--ghost" style="padding:.5rem .95rem;font-size:.88rem"><i class="bi bi-arrow-left"></i> Back to home</a>
      <form class="cat-filter" role="search" onsubmit="return false">
        <div class="ui-inputwrap">
          <input id="cf-search" class="ui-input" type="search" placeholder="Search courses…" autocomplete="off" aria-label="Search courses">
          <i class="bi bi-search" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none"></i>
        </div>
        <select id="cf-status" class="ui-input cat-select" aria-label="Filter by status">
          <option value="all">All courses</option>
          <option value="owned">Owned</option>
          <option value="available">Available</option>
        </select>
      </form>
    </div>
    <p class="k">Category</p>
    <h1><?= e($category['title'] ?? 'Courses') ?></h1>
    <p>Courses to expand your skills and boost your career &amp; salary.</p>
  </section>

  <!-- Courses -->
  <section class="page section" style="padding-top:1rem">
    <?php if (empty($courses)): ?>
      <div class="empty-block"><i class="bi bi-book"></i>No courses in this category yet — check back soon.</div>
    <?php else: ?>
      <div class="course-grid">
        <?php foreach ($courses as $i => $course): $isPaid = !empty($course['paid']); $inCart = !empty($course['in_cart']); ?>
          <article class="crs" data-status="<?= $isPaid ? 'owned' : 'available' ?>" data-title="<?= e(strtolower((string) $course['title'])) ?>" style="animation:ui-fadeup .5s <?= 0.04 * $i ?>s both">
            <div class="crs__media">
              <img src="uploading/<?= e($course['image_courses']) ?>" alt="" onerror="this.style.display='none'">
              <?php if ($isPaid): ?>
                <span class="crs__owned"><i class="bi bi-check-circle-fill"></i> Owned</span>
              <?php elseif ($inCart): ?>
                <span class="crs__owned" style="background:var(--accent);color:var(--accent-ink)"><i class="bi bi-bag-check-fill"></i> In cart</span>
              <?php else: ?>
                <form action="/course" method="post">
                  <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                  <input type="hidden" name="id" value="<?= (int) $categoryId ?>">
                  <input type="hidden" name="email" value="<?= e($email) ?>">
                  <button type="submit" class="crs__cart" title="Add to cart" aria-label="Add to cart"><i class="bi bi-cart-plus"></i></button>
                </form>
              <?php endif; ?>
            </div>
            <div class="crs__body">
              <h3 class="crs__title"><?= e($course['title']) ?></h3>
              <p class="crs__desc"><?= e($course['description'] ?? '') ?></p>
              <div class="crs__foot">
                <span class="crs__enroll"><span class="ic"><i class="bi bi-mortarboard-fill"></i></span> Lifetime</span>
                <?php if ($isPaid): ?>
                  <form action="/blog_learning" method="post">
                    <input type="hidden" name="email" value="<?= e($email) ?>">
                    <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                    <button type="submit" class="crs__join"><i class="bi bi-play-fill"></i> Start</button>
                  </form>
                <?php else: ?>
                  <span class="crs__price"><?= e($course['price']) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div id="cf-none" class="empty-block" style="display:none"><i class="bi bi-search"></i>No courses match your filter.</div>
    <?php endif; ?>
  </section>

  <!-- CTA -->
  <section class="page">
    <div class="cta-strip">
      <div>
        <h3>Not sure where to start?</h3>
        <p>Browse every category and build your own learning path.</p>
      </div>
      <a href="/student" class="ui-btn ui-btn--primary"><i class="bi bi-grid"></i> All categories</a>
    </div>
  </section>

  <footer class="foot">
    <div class="foot__wrap">
      <span>© <?= date('Y') ?> E‑Learning — grow your skills.</span>
      <div class="foot__soc">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </footer>

  <script>
  // Client-side course filter: search by title + status (all/owned/available).
  (function () {
    var search = document.getElementById('cf-search');
    var status = document.getElementById('cf-status');
    if (!search || !status) return;
    var cards = [].slice.call(document.querySelectorAll('.course-grid .crs'));
    var none = document.getElementById('cf-none');
    function apply() {
      var q = (search.value || '').trim().toLowerCase();
      var st = status.value;
      var shown = 0;
      cards.forEach(function (c) {
        var okText = !q || (c.getAttribute('data-title') || '').indexOf(q) > -1;
        var okStatus = st === 'all' || c.getAttribute('data-status') === st;
        var show = okText && okStatus;
        c.style.display = show ? '' : 'none';
        if (show) shown++;
      });
      if (none) none.style.display = shown ? 'none' : '';
    }
    search.addEventListener('input', apply);
    status.addEventListener('change', apply);
  })();
  </script>
  <script src="assets/theme.js"></script>
</body>
</html>
