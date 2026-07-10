<?php
/**
 * Student classroom for a purchased course. Standalone page in the shared
 * student design system (light/dark).
 *
 * @var array $student  logged-in student (email, name, profile_image …)
 * @var array $course   the course being studied (title, user_id …)
 * @var int   $courseId the course id
 * @var array $lessons  lessons for the course, each with a `quizzes` list
 * @var array $teacher  the course's trainer (name, email, phone, profile_image)
 */

use App\Core\View;

$student   = $student ?? [];
$course    = $course ?? [];
$courseId  = $courseId ?? 0;
$lessons   = $lessons ?? [];
$teacher   = $teacher ?? [];
$email     = $student['email'] ?? '';

$quizCount = 0;
foreach ($lessons as $l) {
    $quizCount += count($l['quizzes'] ?? []);
}
$tName = $teacher['name'] ?? '';
$tInit = strtoupper(substr((string) ($tName ?: 'T'), 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($course['title'] ?? 'Classroom') ?> — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>
    .d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    /* Hero */
    .cls-hero{padding:2.4rem 0 .5rem}
    .cls-hero h1{font-family:var(--serif);font-weight:600;font-size:clamp(2rem,4vw,2.9rem);line-height:1.05;margin:.5rem 0 .4rem;letter-spacing:-.01em}
    .cls-hero h1 em{font-style:italic;color:var(--accent)}
    .cls-hero p.lead{color:var(--muted);max-width:56ch;margin:0}
    /* Tabs */
    .cls-tabs{display:inline-flex;gap:.3rem;background:var(--surface-2);border:1px solid var(--line);border-radius:999px;padding:.35rem;margin-top:1.6rem;flex-wrap:wrap}
    .cls-tab{border:none;background:transparent;color:var(--muted);font-family:inherit;font-weight:600;font-size:.9rem;padding:.55rem 1.25rem;border-radius:999px;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;transition:background .2s,color .2s}
    .cls-tab .n{font-size:.72rem;background:var(--bg-tint-1);color:var(--accent);border-radius:999px;padding:.05rem .5rem}
    .cls-tab.is-active{background:var(--accent);color:var(--accent-ink);box-shadow:var(--shadow)}
    .cls-tab.is-active .n{background:rgba(0,0,0,.14);color:inherit}
    .cls-panel{display:none;animation:ui-fadeup .4s both}
    .cls-panel.is-active{display:block}
    /* Lessons */
    .lesson-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:1.5rem}
    .lesson{background:var(--surface);border:1px solid var(--line);border-radius:20px;overflow:hidden;box-shadow:var(--shadow);display:flex;flex-direction:column;transition:transform .3s,box-shadow .3s}
    .lesson:hover{transform:translateY(-5px);box-shadow:0 22px 46px -22px rgba(0,0,0,.4)}
    .lesson__media{position:relative;aspect-ratio:16/9;background:var(--surface-3)}
    .lesson__media iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
    .lesson__body{padding:1.15rem 1.3rem 1.35rem}
    .lesson__num{display:inline-flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);margin-bottom:.4rem}
    .lesson h3{font-family:var(--serif);font-weight:600;font-size:1.2rem;margin:0 0 .35rem}
    .lesson p{color:var(--muted);font-size:.9rem;margin:0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
    /* Quiz upload + cards */
    .quiz-upload{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:1.6rem;box-shadow:var(--shadow);margin-bottom:2rem}
    .quiz-upload .row{display:flex;gap:.8rem;flex-wrap:wrap;align-items:flex-end}
    .quiz-upload .row > *{flex:1;min-width:180px}
    .quiz-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.1rem}
    .quiz{background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:1.3rem;box-shadow:var(--shadow);text-align:center;transition:transform .25s}
    .quiz:hover{transform:translateY(-4px)}
    .quiz .ic{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;margin:0 auto .8rem;background:var(--bg-tint-1);color:var(--accent);font-size:1.4rem}
    .quiz h4{font-family:var(--serif);font-weight:600;font-size:1.02rem;margin:0 0 .9rem}
    /* Trainer */
    .trainer-card{display:flex;gap:1.6rem;align-items:center;background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:2rem;box-shadow:var(--shadow);flex-wrap:wrap}
    .trainer-ava{width:110px;height:110px;border-radius:50%;object-fit:cover;flex:none;display:grid;place-items:center;background:var(--surface-3);color:var(--accent);font-family:var(--serif);font-size:2.6rem;border:3px solid var(--surface-2)}
    .trainer-info h2{font-family:var(--serif);font-weight:600;font-size:1.6rem;margin:0 0 .2rem}
    .trainer-info .role{color:var(--accent);font-weight:700;font-size:.76rem;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.9rem}
    .trainer-meta{display:flex;gap:1.8rem;flex-wrap:wrap}
    .trainer-meta div{display:flex;align-items:center;gap:.55rem;color:var(--muted);font-size:.92rem}
    .trainer-meta i{color:var(--accent)}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'active' => 'home']) ?>

  <!-- Hero -->
  <section class="page cls-hero">
    <a href="/student" class="ui-btn ui-btn--ghost" style="padding:.5rem .95rem;font-size:.88rem;margin-bottom:1.2rem"><i class="bi bi-arrow-left"></i> Back to home</a>
    <p class="k">Classroom</p>
    <h1>Welcome to <em><?= e($course['title'] ?? 'your course') ?></em></h1>
    <p class="lead">Work through each lesson at your own pace, test yourself with quizzes, and reach out to your trainer any time.</p>

    <div class="cls-tabs" role="tablist">
      <button class="cls-tab is-active" data-tab="lessons"><i class="bi bi-play-btn"></i> Lessons <span class="n"><?= count($lessons) ?></span></button>
      <button class="cls-tab" data-tab="quizzes"><i class="bi bi-patch-question"></i> Quizzes <span class="n"><?= $quizCount ?></span></button>
      <button class="cls-tab" data-tab="trainer"><i class="bi bi-person-badge"></i> Trainer</button>
    </div>
  </section>

  <!-- Lessons -->
  <section class="page section cls-panel is-active" id="panel-lessons" style="padding-top:1.4rem">
    <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Course content</p><h2>Lessons</h2></div>
    <?php if (empty($lessons)): ?>
      <div class="empty-block"><i class="bi bi-collection-play"></i>No lessons have been added to this course yet — check back soon.</div>
    <?php else: ?>
      <div class="lesson-grid">
        <?php foreach ($lessons as $i => $lesson): ?>
          <article class="lesson" style="animation:ui-fadeup .5s <?= 0.05 * $i ?>s both">
            <div class="lesson__media">
              <?php if (!empty($lesson['video'])): ?>
                <iframe src="<?= e($lesson['video']) ?>" title="<?= e($lesson['title']) ?>" allowfullscreen loading="lazy"></iframe>
              <?php endif; ?>
            </div>
            <div class="lesson__body">
              <span class="lesson__num"><i class="bi bi-bookmark-fill"></i> Lesson <?= $i + 1 ?></span>
              <h3><?= e($lesson['title']) ?></h3>
              <p><?= e($lesson['description'] ?? '') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Quizzes -->
  <section class="page section cls-panel" id="panel-quizzes" style="padding-top:1.4rem">
    <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Test your understanding</p><h2>Quizzes</h2></div>

    <div class="quiz-upload">
      <h4 class="ui-serif" style="font-family:var(--serif);font-weight:600;margin:0 0 .3rem">Submit your result</h4>
      <p class="ui-muted" style="color:var(--muted);font-size:.9rem;margin:0 0 1.1rem">Take a screenshot of your quiz result and upload it for the lesson you completed.</p>
      <form action="/blog_learning#panel-quizzes" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="ui-field" style="margin:0"><label class="ui-label" for="qimage">Result screenshot</label>
            <input class="ui-input" type="file" id="qimage" name="image" accept="image/*" style="padding:.55rem 1rem"></div>
          <div class="ui-field" style="margin:0"><label class="ui-label" for="lesson_select">Lesson</label>
            <select class="ui-input" id="lesson_select" name="lesson_select">
              <option>Select the lesson</option>
              <?php foreach ($lessons as $lesson): ?>
                <?php if (!empty($lesson['quizzes'])): ?>
                  <option value="<?= e($lesson['title']) ?>"><?= e($lesson['title']) ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select></div>
          <input type="hidden" name="course_id" value="<?= (int) $courseId ?>">
          <button class="ui-btn ui-btn--primary" type="submit" style="flex:0 0 auto"><i class="bi bi-upload"></i> Submit</button>
        </div>
      </form>
    </div>

    <?php if ($quizCount < 1): ?>
      <div class="empty-block"><i class="bi bi-patch-question"></i>No quizzes have been added to this course yet.</div>
    <?php else: ?>
      <div class="quiz-grid">
        <?php foreach ($lessons as $lesson): ?>
          <?php foreach ($lesson['quizzes'] as $quiz): ?>
            <div class="quiz">
              <div class="ic"><i class="bi bi-ui-checks-grid"></i></div>
              <h4><?= e($lesson['title']) ?></h4>
              <a href="<?= e($quiz['content']) ?>" target="_blank" rel="noopener" class="ui-btn ui-btn--ghost ui-btn--block"><i class="bi bi-box-arrow-up-right"></i> Start quiz</a>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Trainer -->
  <section class="page section cls-panel" id="panel-trainer" style="padding-top:1.4rem">
    <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Your instructor</p><h2>Trainer</h2></div>
    <div class="trainer-card">
      <?php if (!empty($teacher['profile_image'])): ?>
        <img class="trainer-ava" src="uploading/<?= e($teacher['profile_image']) ?>" alt="" onerror="this.replaceWith(Object.assign(document.createElement('div'),{className:'trainer-ava',textContent:'<?= e($tInit) ?>'}))">
      <?php else: ?>
        <div class="trainer-ava"><?= e($tInit) ?></div>
      <?php endif; ?>
      <div class="trainer-info">
        <h2><?= e($tName ?: 'Not assigned yet') ?></h2>
        <div class="role">Course Trainer</div>
        <div class="trainer-meta">
          <div><i class="bi bi-telephone-fill"></i> <?= e($teacher['phone'] ?? '—') ?></div>
          <div><i class="bi bi-envelope-fill"></i> <?= e($teacher['email'] ?? '—') ?></div>
        </div>
      </div>
    </div>
  </section>

  <footer class="foot">
    <div class="foot__wrap">
      <span>© <?= date('Y') ?> E‑Learning — keep learning.</span>
      <div class="foot__soc">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
      </div>
    </div>
  </footer>

  <script src="assets/theme.js"></script>
  <script>
    (function(){
      var tabs = document.querySelectorAll('.cls-tab');
      var panels = document.querySelectorAll('.cls-panel');
      function activate(name){
        tabs.forEach(function(t){ t.classList.toggle('is-active', t.dataset.tab === name); });
        panels.forEach(function(p){ p.classList.toggle('is-active', p.id === 'panel-' + name); });
      }
      tabs.forEach(function(t){ t.addEventListener('click', function(){ activate(t.dataset.tab); }); });
      // Deep-link to the quizzes panel after an upload (form posts to #panel-quizzes).
      if (location.hash === '#panel-quizzes') activate('quizzes');
    })();
  </script>
</body>
</html>
