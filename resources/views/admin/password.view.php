<?php
/**
 * Admin change-password screen (standalone).
 *
 * @var bool  $input   whether the form was submitted (show validation state)
 * @var array $require field => error message (currentPassword/newPassword/confirmPassword)
 */
$input   = $input ?? false;
$require = $require ?? ['currentPassword' => '', 'newPassword' => '', 'confirmPassword' => ''];

/** Small helper: the submitted value for a field, only when it had no error. */
$keep = static function (string $field) use ($input, $require): string {
    if ($input && ($require[$field] ?? '') === '') {
        return htmlspecialchars((string) ($_POST[$field] ?? ''), ENT_QUOTES);
    }
    return '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password · E-Learning Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Sora:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap">

    <style>
        :root {
            --bg-0: #14110c;
            --bg-1: #1c1810;
            --amber: #F28500;
            --amber-lt: #ffb454;
            --amber-glow: rgba(242, 133, 0, .55);
            --ink: #f4ede0;
            --muted: #a99e8b;
            --line: rgba(242, 133, 0, .16);
            --danger: #ff6f61;
            --ok: #46d199;
            --panel: rgba(30, 25, 17, .66);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Sora', sans-serif;
            color: var(--ink);
            background: var(--bg-0);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            position: relative;
            overflow-x: hidden;
        }

        /* ---- Atmospheric background: warm aurora + drifting glow ---- */
        .aura {
            position: fixed; inset: -20% -20% -20% -20%;
            z-index: -3;
            background:
                radial-gradient(45% 55% at 18% 22%, rgba(242, 133, 0, .28), transparent 60%),
                radial-gradient(40% 45% at 82% 78%, rgba(255, 180, 84, .18), transparent 62%),
                radial-gradient(60% 60% at 50% 50%, #241d12, var(--bg-0) 70%);
            animation: drift 18s ease-in-out infinite alternate;
            filter: saturate(1.1);
        }
        @keyframes drift {
            from { transform: translate3d(-2%, -1%, 0) scale(1.02); }
            to   { transform: translate3d(3%, 2%, 0) scale(1.06); }
        }
        /* Fine grain overlay */
        .grain {
            position: fixed; inset: 0; z-index: -2; pointer-events: none; opacity: .05;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }
        /* Faint engraved grid */
        .grid {
            position: fixed; inset: 0; z-index: -2; pointer-events: none; opacity: .35;
            background-image:
                linear-gradient(rgba(242,133,0,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(242,133,0,.04) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: radial-gradient(circle at 50% 45%, #000 0%, transparent 72%);
        }

        /* ---- Back link ---- */
        .back {
            position: fixed; top: 1.4rem; left: 1.6rem; z-index: 5;
            display: inline-flex; align-items: center; gap: .5rem;
            font-size: .82rem; font-weight: 500; letter-spacing: .02em;
            color: var(--muted); text-decoration: none;
            padding: .5rem .9rem; border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(20, 17, 12, .5);
            backdrop-filter: blur(8px);
            transition: color .25s, border-color .25s, transform .25s;
        }
        .back:hover { color: var(--amber-lt); border-color: var(--amber); transform: translateX(-2px); }
        .back svg { width: 15px; height: 15px; }

        /* ---- Card ---- */
        .card {
            width: 100%; max-width: 430px;
            background: var(--panel);
            backdrop-filter: blur(22px) saturate(1.2);
            border: 1px solid var(--line);
            border-radius: 26px;
            padding: 2.6rem 2.2rem 2.2rem;
            position: relative;
            box-shadow:
                0 1px 0 rgba(255, 255, 255, .06) inset,
                0 40px 90px -30px rgba(0, 0, 0, .8),
                0 0 60px -20px var(--amber-glow);
        }
        /* Gradient hairline ring */
        .card::before {
            content: ""; position: absolute; inset: 0; border-radius: 26px; padding: 1px;
            background: linear-gradient(150deg, rgba(255,180,84,.5), transparent 40%, transparent 65%, rgba(242,133,0,.35));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            pointer-events: none;
        }

        .badge {
            width: 62px; height: 62px; margin: 0 auto 1.2rem;
            display: grid; place-items: center;
            border-radius: 18px;
            background: linear-gradient(145deg, var(--amber), #c96b00);
            box-shadow: 0 12px 26px -8px var(--amber-glow), 0 0 0 1px rgba(255,255,255,.12) inset;
            color: #1a1206;
            animation: rise .7s cubic-bezier(.2,.7,.2,1) both;
        }
        .badge svg { width: 30px; height: 30px; }

        .card h1 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-weight: 800; font-size: 1.85rem; line-height: 1.05;
            text-align: center; letter-spacing: -.02em;
            animation: rise .7s cubic-bezier(.2,.7,.2,1) .06s both;
        }
        .card h1 em { color: var(--amber-lt); font-style: normal; }
        .sub {
            text-align: center; color: var(--muted);
            font-size: .86rem; margin-top: .45rem; margin-bottom: 2rem;
            animation: rise .7s cubic-bezier(.2,.7,.2,1) .12s both;
        }

        /* ---- Fields ---- */
        .field { margin-bottom: 1.15rem; animation: rise .7s cubic-bezier(.2,.7,.2,1) both; }
        .field:nth-of-type(1) { animation-delay: .18s; }
        .field:nth-of-type(2) { animation-delay: .24s; }
        .field:nth-of-type(3) { animation-delay: .30s; }

        .field label {
            display: block; font-size: .72rem; font-weight: 600;
            letter-spacing: .09em; text-transform: uppercase;
            color: var(--muted); margin-bottom: .5rem;
        }
        .wrap { position: relative; display: flex; align-items: center; }
        .wrap > .lock {
            position: absolute; left: 15px; width: 17px; height: 17px;
            color: var(--muted); pointer-events: none; transition: color .25s;
        }
        .wrap input {
            width: 100%;
            font-family: 'Space Mono', monospace; font-size: .95rem; letter-spacing: .06em;
            color: var(--ink);
            background: rgba(12, 10, 6, .55);
            border: 1px solid var(--line);
            border-radius: 13px;
            padding: .85rem 3rem .85rem 2.6rem;
            transition: border-color .25s, box-shadow .25s, background .25s;
        }
        .wrap input::placeholder { color: #6c6353; letter-spacing: .12em; }
        .wrap input:focus {
            outline: none;
            border-color: var(--amber);
            background: rgba(12, 10, 6, .8);
            box-shadow: 0 0 0 4px rgba(242, 133, 0, .16);
        }
        .wrap input:focus ~ .lock { color: var(--amber-lt); }
        .eye {
            position: absolute; right: 8px; width: 34px; height: 34px;
            display: grid; place-items: center;
            background: transparent; border: 0; border-radius: 9px; cursor: pointer;
            color: var(--muted); transition: color .2s, background .2s, transform .2s;
        }
        .eye:hover { color: var(--amber-lt); background: rgba(242,133,0,.1); }
        .eye:active { transform: scale(.9); }
        .eye svg { width: 18px; height: 18px; }

        .err {
            display: none; color: var(--danger); font-size: .78rem;
            margin-top: .45rem; padding-left: .2rem;
        }
        .err.show { display: block; animation: shake .4s; }

        /* ---- Strength meter ---- */
        .meter { margin-top: .6rem; display: flex; align-items: center; gap: .6rem; }
        .meter .track { flex: 1; height: 5px; border-radius: 99px; background: rgba(255,255,255,.08); overflow: hidden; }
        .meter .fill { height: 100%; width: 0; border-radius: 99px; background: var(--danger); transition: width .35s ease, background .35s ease; }
        .meter .txt { font-size: .7rem; font-weight: 600; letter-spacing: .06em; color: var(--muted); min-width: 52px; text-align: right; }

        .match { font-size: .74rem; margin-top: .45rem; padding-left: .2rem; min-height: 1em; transition: color .2s; }
        .match.ok  { color: var(--ok); }
        .match.no  { color: var(--danger); }

        /* ---- Submit ---- */
        .submit {
            width: 100%; margin-top: 1.4rem;
            font-family: 'Sora', sans-serif; font-weight: 600; font-size: .95rem;
            color: #1a1206;
            background: linear-gradient(135deg, var(--amber-lt), var(--amber));
            border: 0; border-radius: 13px; padding: .95rem;
            cursor: pointer; letter-spacing: .01em;
            box-shadow: 0 14px 30px -12px var(--amber-glow);
            transition: transform .2s, filter .2s, box-shadow .2s;
            animation: rise .7s cubic-bezier(.2,.7,.2,1) .36s both;
        }
        .submit:hover { filter: brightness(1.06); transform: translateY(-2px); box-shadow: 0 20px 40px -12px var(--amber-glow); }
        .submit:active { transform: translateY(0); }

        @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-4px)} 75%{transform:translateX(4px)} }
        @media (prefers-reduced-motion: reduce) { * { animation: none !important; } .aura { animation: none; } }
    </style>
</head>

<body>
    <div class="aura"></div>
    <div class="grid"></div>
    <div class="grain"></div>

    <a href="/admin_home" class="back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to dashboard
    </a>

    <main class="card">
        <div class="badge" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/><circle cx="12" cy="15.5" r="1.4" fill="currentColor" stroke="none"/></svg>
        </div>
        <h1>Change <em>password</em></h1>
        <p class="sub">Update your administrator credentials</p>

        <form action="/admin_password_comfirm" method="post" id="pwForm" autocomplete="off">
            <!-- Current -->
            <div class="field">
                <label for="currentPassword">Current password</label>
                <div class="wrap">
                    <svg class="lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="••••••••" required value="<?= $keep('currentPassword') ?>">
                    <button class="eye" type="button" data-target="currentPassword" aria-label="Show password"></button>
                </div>
                <p class="err<?= $input && $require['currentPassword'] !== '' ? ' show' : '' ?>"><?= htmlspecialchars((string) $require['currentPassword'], ENT_QUOTES) ?></p>
            </div>

            <!-- New -->
            <div class="field">
                <label for="newPassword">New password</label>
                <div class="wrap">
                    <svg class="lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                    <input type="password" id="newPassword" name="newPassword" placeholder="••••••••" required value="<?= $keep('newPassword') ?>">
                    <button class="eye" type="button" data-target="newPassword" aria-label="Show password"></button>
                </div>
                <div class="meter" aria-hidden="true">
                    <div class="track"><div class="fill" id="pwFill"></div></div>
                    <span class="txt" id="pwTxt">—</span>
                </div>
                <p class="err<?= $input && $require['newPassword'] !== '' ? ' show' : '' ?>"><?= htmlspecialchars((string) $require['newPassword'], ENT_QUOTES) ?></p>
            </div>

            <!-- Confirm -->
            <div class="field">
                <label for="confirmPassword">Confirm new password</label>
                <div class="wrap">
                    <svg class="lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required value="<?= $keep('confirmPassword') ?>">
                    <button class="eye" type="button" data-target="confirmPassword" aria-label="Show password"></button>
                </div>
                <p class="match" id="matchMsg"></p>
                <p class="err<?= $input && $require['confirmPassword'] !== '' ? ' show' : '' ?>"><?= htmlspecialchars((string) $require['confirmPassword'], ENT_QUOTES) ?></p>
            </div>

            <button type="submit" class="submit">Change password</button>
        </form>
    </main>

    <script>
        const EYE_OPEN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
        const EYE_OFF  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.1 9.1 0 0 1 12 4c6.5 0 10 7 10 7a13.2 13.2 0 0 1-1.67 2.4M6.6 6.6A13.3 13.3 0 0 0 2 11s3.5 7 10 7a9 9 0 0 0 4.4-1.1"/><path d="m3 3 18 18"/></svg>';

        // Password visibility toggles.
        document.querySelectorAll('.eye').forEach(btn => {
            btn.innerHTML = EYE_OPEN;
            btn.addEventListener('click', () => {
                const inp = document.getElementById(btn.dataset.target);
                const show = inp.type === 'password';
                inp.type = show ? 'text' : 'password';
                btn.innerHTML = show ? EYE_OFF : EYE_OPEN;
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        // Live strength meter (mirrors the app rule: a letter + a digit, min 5 chars).
        const fill = document.getElementById('pwFill');
        const txt  = document.getElementById('pwTxt');
        const newPw = document.getElementById('newPassword');
        const LEVELS = [
            { w: '18%', c: '#ff6f61', t: 'Weak' },
            { w: '55%', c: '#f2a900', t: 'Fair' },
            { w: '80%', c: '#ffb454', t: 'Good' },
            { w: '100%', c: '#46d199', t: 'Strong' },
        ];
        function scorePw(v) {
            if (!v) return -1;
            let s = 0;
            if (v.length >= 5) s++;
            if (v.length >= 10) s++;
            if (/[a-z]/i.test(v) && /[0-9]/.test(v)) s++;
            if (/[^a-z0-9]/i.test(v)) s++;
            return Math.min(s, 4) - 1;
        }
        function renderStrength() {
            const i = scorePw(newPw.value);
            if (i < 0) { fill.style.width = '0'; txt.textContent = '—'; txt.style.color = 'var(--muted)'; return; }
            const lvl = LEVELS[i];
            fill.style.width = lvl.w; fill.style.background = lvl.c;
            txt.textContent = lvl.t; txt.style.color = lvl.c;
            checkMatch();
        }
        newPw.addEventListener('input', renderStrength);

        // Confirm-match hint.
        const confirmPw = document.getElementById('confirmPassword');
        const matchMsg = document.getElementById('matchMsg');
        function checkMatch() {
            if (!confirmPw.value) { matchMsg.textContent = ''; matchMsg.className = 'match'; return; }
            const ok = confirmPw.value === newPw.value;
            matchMsg.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
            matchMsg.className = 'match ' + (ok ? 'ok' : 'no');
        }
        confirmPw.addEventListener('input', checkMatch);

        renderStrength();
    </script>
</body>
</html>
