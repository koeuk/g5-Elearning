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
$lessons    = $lessons ?? [];
$teacher    = $teacher ?? [];
$email      = $student['email'] ?? '';
$quizResult = $quizResult ?? null;

$quizCount = 0;
foreach ($lessons as $l) {
    $quizCount += count($l['quizzes'] ?? []);
}
$tName = $teacher['name'] ?? '';
$tInit = strtoupper(substr((string) ($tName ?: 'T'), 0, 1));

// Free / paid access: owners watch everything; non-owners only lessons flagged
// is_free — the rest are locked with a prompt to buy the course.
$owned      = $owned ?? false;
$isLocked   = static fn (array $l): bool => !$owned && empty($l['is_free']);
$firstIndex = null;                       // first playable lesson for the stage
foreach ($lessons as $li => $ll) {
    if (!$isLocked($ll)) { $firstIndex = $li; break; }
}
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
    /* Lessons — course player (stage on top, playlist below) */
    .lesson__num{display:inline-flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);margin-bottom:.4rem}
    .player{background:var(--surface);border:1px solid var(--line);border-radius:22px;overflow:hidden;box-shadow:var(--shadow);margin-bottom:1.8rem}
    .player__stage{position:relative;aspect-ratio:16/9;background:#000}
    .player__stage iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
    .player__meta{padding:1.25rem 1.5rem 1.5rem}
    .player__meta h3{font-family:var(--serif);font-weight:600;font-size:1.5rem;margin:.15rem 0 .5rem;line-height:1.15}
    .player__meta p{color:var(--muted);margin:0;max-width:70ch}
    .playlist__head{display:flex;align-items:baseline;justify-content:space-between;margin:0 .2rem .8rem}
    .playlist__head span{color:var(--faint);font-size:.85rem;font-weight:600}
    .lesson-list{display:flex;flex-direction:column;gap:.6rem}
    .lrow{display:flex;align-items:center;gap:1rem;width:100%;text-align:left;cursor:pointer;font-family:var(--sans);
      background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:.8rem 1rem;color:var(--text);transition:.2s}
    .lrow:hover{border-color:var(--line-2);transform:translateX(3px)}
    .lrow.is-active{border-color:var(--accent);background:var(--bg-tint-1)}
    .lrow__idx{width:42px;height:42px;border-radius:12px;flex:none;display:grid;place-items:center;font-weight:800;font-size:1.05rem;background:var(--surface-3);color:var(--accent)}
    .lrow.is-active .lrow__idx{background:var(--accent);color:var(--accent-ink)}
    .lrow__text{flex:1;min-width:0}
    .lrow__text strong{display:block;font-family:var(--serif);font-weight:600;font-size:1.06rem;line-height:1.2;overflow-wrap:break-word}
    .lrow__text small{display:block;color:var(--muted);font-size:.85rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .lrow__now{display:none;flex:none;font-size:.68rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--accent);
      background:var(--bg-tint-1);border:1px solid var(--accent);padding:.25rem .55rem;border-radius:99px}
    .lrow__play{flex:none;font-size:1.5rem;color:var(--muted)}
    .lrow:hover .lrow__play{color:var(--accent)}
    .lrow.is-active .lrow__play{display:none}
    .lrow.is-active .lrow__now{display:inline-block}
    /* Quiz upload + cards */
    .quiz-upload{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:1.6rem;box-shadow:var(--shadow);margin-bottom:2rem}
    .quiz-upload .row{display:flex;gap:.8rem;flex-wrap:wrap;align-items:flex-end}
    .quiz-upload .row > *{flex:1;min-width:180px}
    .quiz-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:1.1rem}
    .quiz{background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:1.3rem;box-shadow:var(--shadow);text-align:center;transition:transform .25s}
    .quiz:hover{transform:translateY(-4px)}
    .quiz .ic{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;margin:0 auto .8rem;background:var(--bg-tint-1);color:var(--accent);font-size:1.4rem}
    .quiz h4{font-family:var(--serif);font-weight:600;font-size:1.02rem;margin:0 0 .9rem}
    /* MCQ quiz form */
    .quizform{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:1.7rem;box-shadow:var(--shadow);margin-bottom:1.6rem}
    .quizform__head{display:flex;justify-content:space-between;align-items:baseline;gap:1rem;margin-bottom:1.2rem}
    .quizform__head h3{font-family:var(--serif);font-weight:600;font-size:1.35rem;margin:0}
    .quizform__meta{color:var(--muted);font-size:.85rem;font-weight:600;white-space:nowrap}
    .qblock{border:0;padding:0;margin:0 0 1.4rem;min-width:0}
    .qblock legend{font-weight:600;font-size:1.03rem;margin-bottom:.7rem;display:flex;gap:.6rem;align-items:flex-start;width:100%}
    .qnum{flex:none;width:26px;height:26px;border-radius:8px;display:grid;place-items:center;font-size:.78rem;font-weight:800;background:var(--bg-tint-1);color:var(--accent);margin-top:1px}
    .qopt{display:flex;align-items:center;gap:.7rem;padding:.7rem .95rem;border:1px solid var(--line);border-radius:12px;margin-bottom:.5rem;cursor:pointer;transition:border-color .15s,background .15s}
    .qopt:hover{border-color:var(--accent);background:var(--bg-tint-1)}
    .qopt input{accent-color:var(--accent);flex:none}
    .qopt span{flex:1}
    .qopt.is-correct{border-color:var(--emerald);background:var(--bg-tint-2)}
    .qopt.is-wrong{border-color:var(--rose)}
    .qopt.is-correct i{color:var(--emerald)}
    .qopt.is-wrong i{color:var(--rose)}
    .qresult{display:flex;align-items:center;gap:.65rem;padding:.95rem 1.15rem;border-radius:13px;margin-bottom:1.2rem;font-weight:600;
      background:var(--bg-tint-1);border:1px solid var(--accent);color:var(--text)}
    .qresult.is-pass{border-color:var(--emerald)}
    .qresult i{font-size:1.25rem;color:var(--accent)}
    .qresult.is-pass i{color:var(--emerald)}
    .qresult strong{color:var(--accent)}
    .qresult.is-pass strong{color:var(--emerald)}
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

  <style>
    /* Free / paid lesson access */
    .stage-lock{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.6rem;
      background:rgba(10,8,4,.82);color:#fff;text-align:center;padding:1rem}
    .stage-lock i{font-size:2.4rem;color:var(--accent)}
    .stage-lock p{margin:0;font-weight:600}
    .lbadge{font-size:.62rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;padding:.12rem .5rem;border-radius:999px;vertical-align:middle}
    .lbadge--free{background:rgba(38,182,109,.16);color:#12805a}
    .lbadge--paid{background:var(--bg-tint-1);color:var(--accent)}
    .lrow--locked{opacity:1;cursor:not-allowed}
    .lrow--locked .lrow__idx{background:var(--surface-3);color:var(--muted)}
    .lrow--locked .lrow__play{color:var(--muted)}
    .lrow--locked:hover{transform:none;border-color:var(--line)}
    [data-theme="dark"] .lbadge--free{color:#46d199}
  </style>
  <!-- Lessons -->
  <section class="page section cls-panel is-active" id="panel-lessons" style="padding-top:1.4rem">
    <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Course content</p><h2>Lessons</h2></div>
    <?php if (empty($lessons)): ?>
      <div class="empty-block"><i class="bi bi-collection-play"></i>No lessons have been added to this course yet — check back soon.</div>
    <?php else: ?>
      <?php $first = $firstIndex !== null ? $lessons[$firstIndex] : null; ?>
      <!-- Now-playing stage -->
      <div class="player">
        <div class="player__stage">
          <iframe id="mainVideo" src="<?= e($first['video'] ?? '') ?>" title="<?= e($first['title'] ?? '') ?>" allowfullscreen></iframe>
          <?php if ($first === null): ?>
            <div class="stage-lock"><i class="bi bi-lock-fill"></i><p>Buy this course to unlock its lessons.</p></div>
          <?php endif; ?>
        </div>
        <div class="player__meta">
          <span class="lesson__num"><i class="bi bi-play-circle-fill"></i> Now playing · Lesson <span id="mainNum"><?= $firstIndex !== null ? $firstIndex + 1 : '—' ?></span></span>
          <h3 id="mainTitle"><?= e($first['title'] ?? 'Locked content') ?></h3>
          <p id="mainDesc"><?= e($first['description'] ?? 'Purchase this course to watch its lessons.') ?></p>
        </div>
      </div>

      <!-- Playlist -->
      <div class="playlist__head"><p class="lesson__num" style="margin:0"><i class="bi bi-list-ol"></i> Lessons</p><span><?= count($lessons) ?> lesson<?= count($lessons) === 1 ? '' : 's' ?></span></div>
      <div class="lesson-list">
        <?php foreach ($lessons as $i => $lesson): $locked = $isLocked($lesson); $free = !empty($lesson['is_free']); ?>
          <button type="button" class="lrow<?= $i === $firstIndex ? ' is-active' : '' ?><?= $locked ? ' lrow--locked' : '' ?>"<?= $locked ? ' disabled' : '' ?>
                  data-video="<?= $locked ? '' : e($lesson['video'] ?? '') ?>"
                  data-title="<?= e($lesson['title']) ?>"
                  data-desc="<?= e($lesson['description'] ?? '') ?>"
                  data-num="<?= $i + 1 ?>">
            <span class="lrow__idx"><?= $i + 1 ?></span>
            <span class="lrow__text">
              <strong><?= e($lesson['title']) ?><?php if ($free): ?> <span class="lbadge lbadge--free">Free</span><?php elseif ($locked): ?> <span class="lbadge lbadge--paid">Paid</span><?php endif; ?></strong>
              <small><?= $locked ? 'Buy the course to unlock this lesson.' : e($lesson['description'] ?? '') ?></small>
            </span>
            <?php if ($locked): ?>
              <i class="bi bi-lock-fill lrow__play"></i>
            <?php else: ?>
              <span class="lrow__now">Now playing</span>
              <i class="bi bi-play-circle lrow__play"></i>
            <?php endif; ?>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Quizzes -->
  <section class="page section cls-panel" id="panel-quizzes" style="padding-top:1.4rem">
    <div class="section__head" style="text-align:left;margin:0 0 1.4rem"><p class="k">Test your understanding</p><h2>Quizzes</h2></div>

    <?php if ($quizCount < 1): ?>
      <div class="empty-block"><i class="bi bi-patch-question"></i>No quizzes have been added to this course yet.</div>
    <?php else: ?>
      <?php foreach ($lessons as $lesson): ?>
        <?php if (empty($lesson['quizzes']) || $isLocked($lesson)) {
            continue;
        } ?>
        <?php
            $lid = (int) $lesson['lesson_id'];
            $res = ($quizResult && (int) $quizResult['lesson_id'] === $lid) ? $quizResult : null;
        ?>
        <div class="quizform">
          <div class="quizform__head">
            <h3><?= e($lesson['title']) ?></h3>
            <span class="quizform__meta"><?= count($lesson['quizzes']) ?> question<?= count($lesson['quizzes']) === 1 ? '' : 's' ?></span>
          </div>

          <?php if ($res): ?>
            <div class="qresult<?= $res['score'] === $res['total'] ? ' is-pass' : '' ?>">
              <i class="bi bi-<?= $res['score'] === $res['total'] ? 'patch-check-fill' : 'clipboard-check' ?>"></i>
              You scored <strong><?= (int) $res['score'] ?> / <?= (int) $res['total'] ?></strong><?= $res['score'] === $res['total'] ? ' — perfect!' : '' ?>
            </div>
          <?php endif; ?>

          <form method="post" action="/blog_learning#panel-quizzes">
            <input type="hidden" name="course_id" value="<?= (int) $courseId ?>">
            <input type="hidden" name="quiz_lesson" value="<?= $lid ?>">
            <?php foreach ($lesson['quizzes'] as $qi => $q): ?>
              <fieldset class="qblock">
                <legend><span class="qnum"><?= $qi + 1 ?></span> <?= e($q['question']) ?></legend>
                <?php foreach ($q['options'] as $oi => $opt): ?>
                  <?php
                    $picked    = $res && (int) ($res['given'][$q['quiz_id']] ?? -1) === $oi;
                    $isCorrect = $res && $oi === $q['answer'];
                    $cls = $res ? ($isCorrect ? ' is-correct' : ($picked ? ' is-wrong' : '')) : '';
                  ?>
                  <label class="qopt<?= $cls ?>">
                    <input type="radio" name="answers[<?= (int) $q['quiz_id'] ?>]" value="<?= $oi ?>"
                           <?= $picked ? 'checked' : '' ?> <?= $res ? 'disabled' : 'required' ?>>
                    <span><?= e($opt) ?></span>
                    <?php if ($res && ($isCorrect || $picked)): ?>
                      <i class="bi bi-<?= $isCorrect ? 'check-lg' : 'x-lg' ?>"></i>
                    <?php endif; ?>
                  </label>
                <?php endforeach; ?>
              </fieldset>
            <?php endforeach; ?>

            <?php if (!$res): ?>
              <button type="submit" class="ui-btn ui-btn--primary"><i class="bi bi-check2-circle"></i> Submit answers</button>
            <?php endif; ?>
          </form>

          <?php if ($res): ?>
            <!-- Retake: reload the classroom fresh (no quiz_lesson = no grading). -->
            <form method="post" action="/blog_learning#panel-quizzes" style="margin-top:.6rem">
              <input type="hidden" name="course_id" value="<?= (int) $courseId ?>">
              <button type="submit" class="ui-btn ui-btn--ghost"><i class="bi bi-arrow-repeat"></i> Retake quiz</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
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

    // Course player: click a lesson row to load it into the stage above.
    (function(){
      var stage = document.getElementById('mainVideo');
      if (!stage) return;
      var rows = [].slice.call(document.querySelectorAll('.lesson-list .lrow'));
      rows.forEach(function(row){
        row.addEventListener('click', function(){
          rows.forEach(function(r){ r.classList.remove('is-active'); });
          row.classList.add('is-active');
          var v = row.dataset.video || '';
          if (v) { v += (v.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1'; }
          stage.src = v;
          document.getElementById('mainTitle').textContent = row.dataset.title || '';
          document.getElementById('mainDesc').textContent = row.dataset.desc || '';
          document.getElementById('mainNum').textContent = row.dataset.num || '';
          document.querySelector('.player').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });
    })();
  </script>
</body>
</html>
