<?php
/**
 * Student change-password screen. Standalone page in the shared student design
 * system (light/dark).
 *
 * @var bool  $input   whether the form was submitted (show validation state)
 * @var array $require currentPassword/newPassword/confirmPassword error messages
 */
use App\Core\Auth;
use App\Core\View;

$input   = $input ?? false;
$require = $require ?? ['currentPassword' => '', 'newPassword' => '', 'confirmPassword' => ''];
$student = Auth::user() ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change password — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
  <style>
    .d-none-sm{display:inline}@media(max-width:520px){.d-none-sm{display:none}}
    .pw-wrap{max-width:460px;margin:0 auto;padding:2.5rem 0}
    .pw-card{background:var(--surface);border:1px solid var(--line);border-radius:22px;padding:2rem;animation:ui-fadeup .5s both}
    .pw-icon{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;font-size:1.5rem;
      background:var(--bg-tint-1);color:var(--accent);margin-bottom:1rem}
    .pw-card h1{font-family:var(--serif);font-weight:600;font-size:1.7rem;margin:0 0 .3rem}
    .pw-card .sub{color:var(--muted);margin:0 0 1.6rem;font-size:.94rem}
  </style>
</head>
<body class="ui-scope">
  <?= View::partial('layouts/student/topbar', ['student' => $student, 'active' => 'profile']) ?>

  <section class="page pw-wrap">
    <a href="/student_profile" class="ui-btn ui-btn--ghost" style="padding:.5rem .95rem;font-size:.88rem;margin-bottom:1.4rem"><i class="bi bi-arrow-left"></i> Back to profile</a>

    <div class="pw-card">
      <div class="pw-icon"><i class="bi bi-shield-lock-fill"></i></div>
      <h1>Change password</h1>
      <p class="sub">Keep your account secure with a strong, unique password.</p>

      <form action="/student_password_comfirm" method="post" novalidate>
        <div class="ui-field">
          <label class="ui-label" for="currentPassword">Current password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($input && $require['currentPassword'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="currentPassword" name="currentPassword" placeholder="••••••••">
            <button class="ui-eye" type="button" data-eye="currentPassword" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <span class="ui-err"><?= e($require['currentPassword']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="newPassword">New password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($input && $require['newPassword'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="newPassword" name="newPassword" placeholder="••••••••">
            <button class="ui-eye" type="button" data-eye="newPassword" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <?php if ($require['newPassword'] !== ''): ?><span class="ui-err"><?= e($require['newPassword']) ?></span><?php else: ?><span class="ui-err" style="color:var(--muted);font-weight:500">8+ chars with a letter, number &amp; symbol.</span><?php endif; ?>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="confirmPassword">Confirm new password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($input && $require['confirmPassword'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••">
            <button class="ui-eye" type="button" data-eye="confirmPassword" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <span class="ui-err"><?= e($require['confirmPassword']) ?></span>
        </div>

        <button class="ui-btn ui-btn--primary ui-btn--block" type="submit" style="margin-top:.4rem">
          <i class="bi bi-check2-circle"></i> Update password
        </button>
      </form>
    </div>
  </section>

  <script src="assets/theme.js"></script>
  <script>
    document.querySelectorAll('[data-eye]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var inp = document.getElementById(btn.dataset.eye);
        var show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
      });
    });
  </script>
</body>
</html>
