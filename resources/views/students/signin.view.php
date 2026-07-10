<?php
/**
 * Student sign-in. Standalone page.
 *
 * @var bool               $submitted true after a POST attempt
 * @var array<string,string> $errors  email/password error messages
 * @var array<string,string> $old     repopulation values (email)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign in — E‑Learning</title>
  <script>(function(){try{var s=localStorage.getItem('eLearnTheme');document.documentElement.setAttribute('data-theme',s||((window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'));}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,ital,wght@9..144,0,400;9..144,0,600;9..144,1,500;9..144,1,600&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/student-ui.css" rel="stylesheet">
</head>
<body class="ui-scope">
  <div class="auth">

    <!-- Brand / pitch -->
    <aside class="auth__aside">
      <div class="auth__brand"><span class="auth__dot"><i class="bi bi-mortarboard-fill"></i></span> E‑Learning</div>
      <div class="auth__pitch">
        <h2>Pick up right where you <em>left off.</em></h2>
        <p>Your courses, progress and certificates — all waiting behind one sign‑in.</p>
        <ul class="auth__feats">
          <li><i class="bi bi-check-circle-fill"></i> Lifetime access to every course you own</li>
          <li><i class="bi bi-check-circle-fill"></i> Learn at your own pace, on any device</li>
          <li><i class="bi bi-check-circle-fill"></i> Pay by card or in cash — your choice</li>
        </ul>
      </div>
      <div class="auth__note">© <?= date('Y') ?> E‑Learning. Grow your skills.</div>
    </aside>

    <!-- Form -->
    <main class="auth__main">
      <div class="auth__top">
        <div class="auth__mobilebrand"><span class="auth__dot"><i class="bi bi-mortarboard-fill"></i></span> E‑Learning</div>
        <span></span>
        <button class="theme-toggle" data-theme-toggle aria-label="Toggle dark mode">
          <i class="bi bi-sun-fill ico-sun"></i><i class="bi bi-moon-stars-fill ico-moon"></i>
        </button>
      </div>

      <form class="auth__form" action="/access" method="post" novalidate>
        <p class="auth__eyebrow">Welcome back</p>
        <h1 class="auth__title">Sign in to learn</h1>
        <p class="auth__sub">Enter your details to continue.</p>

        <div class="ui-field">
          <label class="ui-label" for="email">Email address</label>
          <input class="ui-input <?= ($submitted && $errors['email'] !== '') ? 'is-invalid' : '' ?>"
                 type="email" id="email" name="email" placeholder="name@example.com"
                 value="<?= e($old['email']) ?>" autocomplete="email">
          <span class="ui-err"><?= e($errors['email']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="password">Password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($submitted && $errors['password'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
            <button class="ui-eye" type="button" id="togglePw" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <span class="ui-err"><?= e($errors['password']) ?></span>
        </div>

        <button class="ui-btn ui-btn--primary ui-btn--block" type="submit" style="margin-top:.4rem">
          <i class="bi bi-box-arrow-in-right"></i> Sign in
        </button>

        <p class="auth__alt">Don’t have an account? <a href="/signup">Create one</a></p>
      </form>
    </main>
  </div>

  <script src="assets/theme.js"></script>
  <script>
    (function(){
      var pw = document.getElementById('password'), t = document.getElementById('togglePw');
      t.addEventListener('click', function(){
        var show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        t.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
      });
    })();
  </script>
</body>
</html>
