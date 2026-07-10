<?php
/**
 * Student home — course-grid dashboard. Standalone page in the shared student
 * design system (light/dark). Rebuilt from the legacy Eduport template.
 *
 * @var array $student    logged-in student (name, email, user_id, profile_image)
 * @var array $categories each with `course_count` and a `courses` list
 * @var array $courses    each with trainer_name/trainer_image/enrolled/paid/in_cart
 * @var array $topCourses most-purchased courses (title, image_courses, count)
 * @var int   $cartCount  number of items in the cart
 */
use App\Core\View;

$student    = $student ?? [];
$categories = $categories ?? [];
$courses    = $courses ?? [];
$topCourses = $topCourses ?? [];
$cartCount  = $cartCount ?? 0;
$email      = $student['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>.d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}</style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'cartCount' => $cartCount, 'active' => 'home']) ?>

  <!-- Hero -->
  <section class="page hero-home">
    <p class="k">Welcome back<?= $student ? ', ' . e($student['name'] ?? '') : '' ?></p>
    <h1>Learn something <em>new</em> today.</h1>
    <p>Explore hand‑picked courses, track what you own, and pick up right where you left off.</p>
    <div class="hero-home__cta">
      <a href="#courses" class="ui-btn ui-btn--primary"><i class="bi bi-collection-play"></i> Browse courses</a>
      <a href="/orders" class="ui-btn ui-btn--ghost"><i class="bi bi-bag-heart"></i> View cart<?= (int) $cartCount > 0 ? ' (' . (int) $cartCount . ')' : '' ?></a>
    </div>
  </section>

  <!-- Categories -->
  <?php if (!empty($categories)): ?>
  <section class="page section" id="categories">
    <div class="section__head"><p class="k">Explore</p><h2>Browse by category</h2></div>
    <div class="cat-grid">
      <?php foreach ($categories as $cate): ?>
        <form action="/course" method="post" style="display:block">
          <input type="hidden" name="id" value="<?= (int) $cate['category_id'] ?>">
          <input type="hidden" name="email" value="<?= e($email) ?>">
          <button type="submit" class="cat-card">
            <img src="uploading/<?= e($cate['image'] ?? '') ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'cat-card__ph',innerHTML:'<i class=&quot;bi bi-folder2-open&quot;></i>'}))">
            <span class="cat-card__t">
              <h5><?= e($cate['title']) ?></h5>
              <span><?= (int) ($cate['course_count'] ?? 0) ?> course<?= (int) ($cate['course_count'] ?? 0) === 1 ? '' : 's' ?></span>
            </span>
          </button>
        </form>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- Featured courses -->
  <section class="page section" id="courses">
    <div class="section__head"><p class="k">Featured</p><h2>Courses for you</h2><p>Top picks across every category.</p></div>
    <?php if (empty($courses)): ?>
      <div class="empty-block"><i class="bi bi-collection"></i>No courses available yet — check back soon.</div>
    <?php else: ?>
      <div class="course-grid">
        <?php foreach ($courses as $i => $course): $isPaid = !empty($course['paid']); $inCart = !empty($course['in_cart']); ?>
          <article class="crs" style="animation:ui-fadeup .5s <?= 0.04 * $i ?>s both">
            <div class="crs__media">
              <img src="uploading/<?= e($course['image_courses']) ?>" alt="" onerror="this.style.display='none'">
              <?php if ($isPaid): ?>
                <span class="crs__owned"><i class="bi bi-check-circle-fill"></i> Owned</span>
              <?php elseif ($inCart): ?>
                <span class="crs__owned" style="background:var(--accent);color:var(--accent-ink)"><i class="bi bi-bag-check-fill"></i> In cart</span>
              <?php else: ?>
                <form action="/student" method="post">
                  <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                  <input type="hidden" name="email" value="<?= e($email) ?>">
                  <button type="submit" class="crs__cart" title="Add to cart" aria-label="Add to cart"><i class="bi bi-cart-plus"></i></button>
                </form>
              <?php endif; ?>
            </div>
            <div class="crs__body">
              <h3 class="crs__title"><?= e($course['title']) ?></h3>
              <div class="crs__meta">
                <img class="crs__ava" src="uploading/<?= e($course['trainer_image'] ?? '') ?>" alt="" onerror="this.style.visibility='hidden'">
                <span><?= e($course['trainer_name'] ?? 'Trainer') ?></span>
              </div>
              <div class="crs__foot">
                <span class="crs__enroll"><span class="ic"><i class="bi bi-people-fill"></i></span><?= (int) ($course['enrolled'] ?? 0) ?></span>
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
    <?php endif; ?>
  </section>

  <!-- CTA -->
  <section class="page">
    <div class="cta-strip">
      <div>
        <h3>Earn a certificate</h3>
        <p>Finish a course and get a shareable certificate to show what you’ve learned.</p>
      </div>
      <a href="#courses" class="ui-btn ui-btn--primary"><i class="bi bi-award"></i> Start learning</a>
    </div>
  </section>

  <!-- Top courses -->
  <?php if (!empty($topCourses)): ?>
  <section class="page section" id="best">
    <div class="section__head"><p class="k">Most popular</p><h2>Top courses this week</h2></div>
    <div class="top-grid">
      <?php foreach ($topCourses as $top): ?>
        <div class="top-tile">
          <img src="uploading/<?= e($top['image_courses']) ?>" alt="" onerror="this.style.opacity=.15">
          <div class="top-tile__grad"></div>
          <div class="top-tile__c">
            <h5><?= e($top['title']) ?></h5>
            <span><i class="bi bi-people"></i> <?= (int) $top['count'] ?> students</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- Footer -->
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

  <script src="assets/theme.js"></script>
</body>
</html>
