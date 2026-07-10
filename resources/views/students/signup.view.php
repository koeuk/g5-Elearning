<?php
/**
 * Student registration. Standalone page.
 *
 * @var bool                 $submitted true after a POST attempt
 * @var array<string,string> $errors    name/email/password/password_comfirm/phone messages
 * @var array<string,string> $old        repopulation values (name/email/phone/gender)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create account — E‑Learning</title>
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
        <h2>Start something <em>worth learning.</em></h2>
        <p>Create your free account and join thousands of learners levelling up every day.</p>
        <ul class="auth__feats">
          <li><i class="bi bi-stars"></i> Hand‑picked courses across every field</li>
          <li><i class="bi bi-award-fill"></i> Earn certificates as you finish</li>
          <li><i class="bi bi-lightning-charge-fill"></i> Get started in under a minute</li>
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

      <form class="auth__form" action="/create_student" method="post" enctype="multipart/form-data" novalidate style="max-width:440px">
        <p class="auth__eyebrow">Join us</p>
        <h1 class="auth__title">Create your account</h1>
        <p class="auth__sub">It’s free — start learning today.</p>

        <div class="ui-field">
          <label class="ui-label" for="name">Full name</label>
          <input class="ui-input <?= ($submitted && $errors['name'] !== '') ? 'is-invalid' : '' ?>"
                 type="text" id="name" name="name" placeholder="Jane Doe" value="<?= e($old['name']) ?>">
          <span class="ui-err"><?= e($errors['name']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="phone">Phone</label>
          <input class="ui-input <?= ($submitted && $errors['phone'] !== '') ? 'is-invalid' : '' ?>"
                 type="text" id="phone" name="phone" placeholder="0XX XXX XXX" value="<?= e($old['phone']) ?>">
          <span class="ui-err"><?= e($errors['phone']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="email">Email address</label>
          <input class="ui-input <?= ($submitted && $errors['email'] !== '') ? 'is-invalid' : '' ?>"
                 type="email" id="email" name="email" placeholder="name@example.com" value="<?= e($old['email']) ?>">
          <span class="ui-err"><?= e($errors['email']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="password">Password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($submitted && $errors['password'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="password" name="password" placeholder="••••••••">
            <button class="ui-eye" type="button" data-eye="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <span class="ui-err"><?= $errors['password'] !== '' ? e($errors['password']) : '8+ chars with a letter, number &amp; symbol.' ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="password_comfirm">Confirm password</label>
          <div class="ui-inputwrap">
            <input class="ui-input <?= ($submitted && $errors['password_comfirm'] !== '') ? 'is-invalid' : '' ?>"
                   type="password" id="password_comfirm" name="password_comfirm" placeholder="••••••••">
            <button class="ui-eye" type="button" data-eye="password_comfirm" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
          <span class="ui-err"><?= e($errors['password_comfirm']) ?></span>
        </div>

        <div class="ui-field">
          <label class="ui-label">Gender</label>
          <div class="ui-radio-row">
            <div class="ui-radio">
              <input type="radio" id="g-male" name="gender" value="Male" <?= ($old['gender'] === 'Male') ? 'checked' : '' ?>>
              <label for="g-male"><i class="bi bi-gender-male"></i> Male</label>
            </div>
            <div class="ui-radio">
              <input type="radio" id="g-female" name="gender" value="Female" <?= ($old['gender'] !== 'Male') ? 'checked' : '' ?>>
              <label for="g-female"><i class="bi bi-gender-female"></i> Female</label>
            </div>
          </div>
        </div>

        <div class="ui-field">
          <label class="ui-label" for="image">Profile photo <span class="ui-muted" style="font-weight:400">(optional)</span></label>
          <input class="ui-input" type="file" id="image" name="image" accept="image/*" style="padding:.6rem 1rem">
        </div>

        <button class="ui-btn ui-btn--primary ui-btn--block" type="submit" style="margin-top:.4rem">
          <i class="bi bi-person-plus-fill"></i> Create account
        </button>

        <p class="auth__alt">Already have an account? <a href="/signin">Sign in</a></p>
      </form>
    </main>
  </div>

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
