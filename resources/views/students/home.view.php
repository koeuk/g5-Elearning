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
  <style>.d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    .crs__view{background:transparent;color:var(--accent);border:1px solid var(--accent);border-radius:10px;padding:.5rem .85rem;font-weight:700;font-size:.85rem;cursor:pointer;font-family:var(--sans);display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;transition:.2s}
    .crs__view:hover{background:var(--accent);color:var(--accent-ink)}
    .crs__foot-right{display:flex;align-items:center;gap:.7rem}
    /* Hero: text (left) + clock (right) in one row */
    #heroMain{text-align:left}
    .hero-row{display:flex;align-items:center;justify-content:space-between;gap:2.5rem;flex-wrap:wrap}
    .hero-left{flex:1 1 480px;min-width:300px}
    #heroMain h1{margin:0;max-width:20ch}
    #heroMain > .hero-row .hero-home__cta{justify-content:flex-start;margin-top:1.6rem}
    .hero-clock{flex:none;
      background:var(--surface);border:1px solid var(--line);border-radius:22px;
      padding:1.5rem 2rem;text-align:center;min-width:230px;animation:ui-fadeup .6s .1s both}
    .hero-clock__ic{width:46px;height:46px;border-radius:14px;display:grid;place-items:center;margin:0 auto .7rem;
      background:var(--bg-tint-1);color:var(--accent);font-size:1.35rem}
    .hero-clock__time{font-family:var(--serif);font-weight:600;font-size:2.5rem;line-height:1;color:var(--text);letter-spacing:.01em}
    .hero-clock__date{color:var(--muted);font-size:.92rem;margin-top:.5rem;font-weight:600}
    @media(max-width:1150px){.hero-row{justify-content:center;text-align:center}#heroMain h1{max-width:none}#heroMain > .hero-row .hero-home__cta{justify-content:center}.hero-clock{margin:.5rem auto 0}}
    /* Featured video card */
    .video-card{border-radius:22px;overflow:hidden;border:1px solid var(--line);background:var(--surface);max-width:920px;margin:0 auto}
    .video-card__stage{position:relative;aspect-ratio:16/9;background:var(--surface-3);cursor:pointer;overflow:hidden}
    .video-card__stage img{width:100%;height:100%;object-fit:cover;display:block;transition:.4s}
    .video-card__stage:hover img{transform:scale(1.04)}
    .video-card__stage iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
    .video-card__grad{position:absolute;inset:0;background:linear-gradient(180deg,transparent 52%,rgba(0,0,0,.4));pointer-events:none}
    .video-card__play{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:78px;height:78px;border-radius:50%;border:none;
      background:var(--accent);color:var(--accent-ink);font-size:2.1rem;display:grid;place-items:center;cursor:pointer;transition:.2s;box-shadow:0 12px 34px -8px var(--accent)}
    .video-card__stage:hover .video-card__play{transform:translate(-50%,-50%) scale(1.08)}
    .video-card__cap{position:absolute;left:1.4rem;bottom:1.1rem;color:#fff;font-family:var(--serif);font-weight:600;font-size:1.2rem;z-index:2;text-shadow:0 2px 12px rgba(0,0,0,.5)}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'cartCount' => $cartCount, 'active' => 'home']) ?>

  <!-- Hero -->
  <section class="page hero-home" id="heroMain">
    <div class="hero-row">
      <div class="hero-left">
        <p class="k">Welcome back<?= $student ? ', ' . e($student['name'] ?? '') : '' ?></p>
        <h1>Learn something <em>new</em> today.</h1>
        <p>Explore hand‑picked courses, track what you own, and pick up right where you left off.</p>
        <div class="hero-home__cta">
          <a href="#courses" class="ui-btn ui-btn--primary"><i class="bi bi-collection-play"></i> Browse courses</a>
          <a href="/orders" class="ui-btn ui-btn--ghost"><i class="bi bi-bag-heart"></i> View cart<?= (int) $cartCount > 0 ? ' (' . (int) $cartCount . ')' : '' ?></a>
        </div>
      </div>

      <!-- Live clock -->
      <div class="hero-clock" id="heroClock">
        <div class="hero-clock__ic"><i class="bi bi-clock-history"></i></div>
        <div class="hero-clock__time" id="hcTime">--:--</div>
        <div class="hero-clock__date" id="hcDate">&nbsp;</div>
      </div>
    </div>
  </section>

  <!-- Featured video -->
  <section class="page section" id="intro-video" style="padding-top:1rem">
    <div class="section__head"><p class="k">Watch</p><h2>How E‑Learning works</h2></div>
    <div class="video-card">
      <div class="video-card__stage" id="videoStage" data-embed="https://www.youtube.com/embed/tXHviS-4ygo?autoplay=1&rel=0">
        <img src="assets/images/about/12.jpg" alt="Intro video" onerror="this.style.opacity=.25">
        <div class="video-card__grad"></div>
        <span class="video-card__cap">A quick tour of your learning hub</span>
        <button class="video-card__play" type="button" aria-label="Play video"><i class="bi bi-play-fill"></i></button>
      </div>
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
                  <span class="crs__foot-right">
                    <span class="crs__price"><?= e($course['price']) ?></span>
                    <form action="/blog_learning" method="post" style="margin:0">
                      <input type="hidden" name="email" value="<?= e($email) ?>">
                      <input type="hidden" name="course_id" value="<?= (int) $course['course_id'] ?>">
                      <button type="submit" class="crs__view" title="Preview free lessons"><i class="bi bi-eye"></i> View</button>
                    </form>
                  </span>
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

  <script>
  // Live hero clock — ticks every second.
  (function () {
    var t = document.getElementById('hcTime'), d = document.getElementById('hcDate');
    if (!t || !d) return;
    function tick() {
      var now = new Date();
      t.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      d.textContent = now.toLocaleDateString([], { weekday: 'long', month: 'short', day: 'numeric' });
    }
    tick();
    setInterval(tick, 1000);
  })();
  // Featured video — swap the thumbnail for the player on click.
  (function () {
    var stage = document.getElementById('videoStage');
    if (!stage) return;
    stage.addEventListener('click', function () {
      stage.innerHTML = '<iframe src="' + stage.dataset.embed + '" title="Intro video" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
    });
  })();
  </script>
  <script src="assets/theme.js"></script>
</body>
</html>
