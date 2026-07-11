<?php
/**
 * "My Courses" — only the courses this student has purchased. Same student
 * design system (light/dark) as the home grid; each card opens the classroom.
 *
 * @var array $student   logged-in student (name, email, user_id, profile_image)
 * @var array $courses   purchased courses (trainer_name/trainer_image/enrolled)
 * @var int   $cartCount number of items in the cart
 */
use App\Core\View;

$student   = $student ?? [];
$courses   = $courses ?? [];
$cartCount = $cartCount ?? 0;
$email     = $student['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Courses — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>.d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}</style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'cartCount' => $cartCount, 'active' => 'courses']) ?>

  <section class="page section" id="my-courses">
    <div class="section__head">
      <p class="k">Your library</p>
      <h2>My Courses</h2>
      <p>Courses you own — pick up right where you left off.</p>
    </div>

    <?php if (empty($courses)): ?>
      <div class="empty-block">
        <i class="bi bi-bag-check"></i>
        You haven't bought any courses yet.
        <a href="/student" class="ui-btn ui-btn--primary" style="margin-top:1rem"><i class="bi bi-collection"></i> Browse courses</a>
      </div>
    <?php else: ?>
      <div class="course-grid">
        <?php foreach ($courses as $i => $course): ?>
          <article class="crs" style="animation:ui-fadeup .5s <?= 0.04 * $i ?>s both">
            <div class="crs__media">
              <img src="uploading/<?= e($course['image_courses']) ?>" alt="" onerror="this.style.display='none'">
              <span class="crs__owned"><i class="bi bi-check-circle-fill"></i> Owned</span>
            </div>
            <div class="crs__body">
              <h3 class="crs__title"><?= e($course['title']) ?></h3>
              <div class="crs__meta">
                <img class="crs__ava" src="uploading/<?= e($course['trainer_image'] ?? '') ?>" alt="" onerror="this.style.visibility='hidden'">
                <span><?= e($course['trainer_name'] ?? 'Trainer') ?></span>
              </div>
              <div class="crs__foot">
                <span class="crs__enroll"><span class="ic"><i class="bi bi-people-fill"></i></span><?= (int) ($course['enrolled'] ?? 0) ?></span>
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
  </section>

  <script src="assets/theme.js"></script>
</body>
</html>
