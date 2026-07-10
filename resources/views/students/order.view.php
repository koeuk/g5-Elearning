<?php
/**
 * Student ordering / checkout screen. Standalone page (own <head>/<body>).
 *
 * Request handling (recording payments) now lives in
 * App\Controllers\Student\OrderController; this template only renders.
 *
 * @var string             $email  acting student's email (for the hidden form fields)
 * @var array<int, array>  $orders pending cart rows, each joined with course title/price/image
 * @var bool               $paid   true when a payment was just completed (shows the banner)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — E-Learning</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0c0d11; --surface:#14161d; --surface-2:#1b1e28; --surface-3:#222733;
      --line:rgba(255,255,255,.08); --line-2:rgba(255,255,255,.14);
      --text:#f4f1ea; --muted:#9aa0ad; --faint:#6b7280;
      --accent:#f5a524; --accent-2:#ffcf6b; --emerald:#2ee6a6; --rose:#ff6b6b;
      --shadow:0 24px 60px -20px rgba(0,0,0,.7);
      --serif:"Fraunces",Georgia,serif; --sans:"Hanken Grotesk",system-ui,sans-serif;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{min-height:100%}
    body{
      font-family:var(--sans); color:var(--text); background:var(--bg);
      background-image:
        radial-gradient(1200px 600px at 88% -12%, rgba(245,165,36,.16), transparent 58%),
        radial-gradient(900px 520px at -12% 18%, rgba(46,230,166,.09), transparent 55%);
      background-attachment:fixed;
      -webkit-font-smoothing:antialiased; letter-spacing:.005em; line-height:1.5;
      padding-bottom:4rem; position:relative;
    }
    body::before{ /* grain */
      content:""; position:fixed; inset:0; pointer-events:none; z-index:0; opacity:.035;
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }
    .wrap{position:relative; z-index:1; max-width:1180px; margin:0 auto; padding:0 1.5rem}

    /* ---- Top bar ---- */
    .topbar{
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
      padding:1.6rem 0 1.2rem;
    }
    .brand{display:flex; align-items:center; gap:.7rem; font-weight:800; letter-spacing:-.01em}
    .brand__dot{width:34px;height:34px;border-radius:11px;display:grid;place-items:center;
      background:linear-gradient(140deg,var(--accent),#e07b1a); color:#1a1204; font-size:1.1rem; box-shadow:0 8px 20px -8px rgba(245,165,36,.7)}
    .back{display:inline-flex;align-items:center;gap:.5rem;color:var(--muted);text-decoration:none;
      font-weight:600;font-size:.92rem;padding:.5rem .9rem;border:1px solid var(--line);border-radius:99px;
      transition:.25s}
    .back:hover{color:var(--text);border-color:var(--line-2);transform:translateX(-2px)}
    .pill-count{font-size:.8rem;color:var(--accent);background:rgba(245,165,36,.12);
      border:1px solid rgba(245,165,36,.25);padding:.25rem .6rem;border-radius:99px;font-weight:700}

    /* ---- Hero heading ---- */
    .hero{padding:1rem 0 2.2rem; max-width:640px; animation:fadeUp .7s cubic-bezier(.2,.7,.3,1) both}
    .hero .eyebrow{color:var(--accent);font-weight:700;text-transform:uppercase;letter-spacing:.18em;font-size:.74rem;margin-bottom:.7rem}
    .hero h1{font-family:var(--serif);font-weight:600;font-size:clamp(2.4rem,5.5vw,3.6rem);line-height:1.02;letter-spacing:-.02em}
    .hero h1 em{font-style:italic;color:var(--accent-2)}
    .hero p{color:var(--muted);margin-top:.9rem;font-size:1.02rem;max-width:44ch}

    /* ---- Success banner ---- */
    .paid-banner{
      display:flex;align-items:center;gap:1rem;margin:0 0 1.6rem;padding:1.1rem 1.3rem;border-radius:16px;
      background:linear-gradient(120deg,rgba(46,230,166,.14),rgba(46,230,166,.05));
      border:1px solid rgba(46,230,166,.3); animation:fadeUp .6s both;
    }
    .paid-banner i{font-size:1.6rem;color:var(--emerald)}
    .paid-banner b{font-family:var(--serif);font-weight:600;font-size:1.15rem}
    .paid-banner span{color:var(--muted);font-size:.9rem}
    .paid-banner .close{margin-left:auto;color:var(--muted);background:none;border:none;cursor:pointer;font-size:1.2rem}

    /* ---- Layout ---- */
    .grid{display:grid;grid-template-columns:1fr 380px;gap:1.8rem;align-items:start}
    @media(max-width:900px){.grid{grid-template-columns:1fr}}

    .panel-label{display:flex;align-items:baseline;justify-content:space-between;margin:0 .3rem 1rem}
    .panel-label h2{font-family:var(--serif);font-weight:600;font-size:1.4rem;letter-spacing:-.01em}
    .panel-label small{color:var(--faint);font-size:.85rem}

    /* ---- Course rows ---- */
    .courses{display:flex;flex-direction:column;gap:.9rem}
    .course{
      display:flex;align-items:center;gap:1.1rem;padding:.9rem;border-radius:18px;
      background:linear-gradient(180deg,var(--surface),rgba(20,22,29,.6));
      border:1px solid var(--line); transition:.3s cubic-bezier(.2,.7,.3,1);
      animation:fadeUp .6s both;
    }
    .course:hover{border-color:var(--line-2);transform:translateY(-3px);box-shadow:var(--shadow)}
    .course__img{width:84px;height:84px;border-radius:14px;object-fit:cover;flex:none;
      background:var(--surface-3);border:1px solid var(--line)}
    .course__body{flex:1;min-width:0}
    .course__title{font-family:var(--serif);font-weight:600;font-size:1.12rem;line-height:1.2;
      overflow-wrap:break-word}
    .course__tag{display:inline-flex;align-items:center;gap:.35rem;margin-top:.45rem;color:var(--muted);
      font-size:.82rem;font-weight:500}
    .course__tag i{color:var(--accent)}
    .course__side{display:flex;flex-direction:column;align-items:flex-end;gap:.6rem;flex:none}
    .course__price{font-weight:800;font-size:1.25rem;letter-spacing:-.01em}
    .course__price::before{content:"$";color:var(--accent);font-size:.85em;margin-right:1px}
    .add-btn{
      cursor:pointer;border:1px solid var(--line-2);background:var(--surface-3);color:var(--text);
      font-family:var(--sans);font-weight:700;font-size:.85rem;padding:.5rem 1.1rem;border-radius:99px;
      transition:.22s;display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap
    }
    .add-btn:hover{border-color:var(--accent);color:var(--accent)}
    .add-btn.is-added{background:var(--accent);border-color:var(--accent);color:#1a1204}
    .add-btn.is-added::before{content:"\F26E";font-family:"bootstrap-icons"}

    .empty{
      text-align:center;padding:3.5rem 1rem;border:1px dashed var(--line-2);border-radius:18px;
      background:rgba(255,255,255,.015)
    }
    .empty i{font-size:2.4rem;color:var(--faint)}
    .empty p{color:var(--muted);margin:.8rem 0 1.2rem}
    .empty a{color:#1a1204;background:var(--accent);text-decoration:none;font-weight:700;
      padding:.65rem 1.3rem;border-radius:99px;display:inline-block;transition:.2s}
    .empty a:hover{transform:translateY(-2px)}

    /* ---- Summary (sticky) ---- */
    .summary{position:sticky;top:1.5rem;border-radius:22px;overflow:hidden;
      background:linear-gradient(180deg,var(--surface-2),var(--surface));
      border:1px solid var(--line);box-shadow:var(--shadow);animation:fadeUp .7s .1s both}
    .summary__head{padding:1.3rem 1.4rem;border-bottom:1px solid var(--line);
      display:flex;align-items:center;gap:.6rem}
    .summary__head i{color:var(--accent)}
    .summary__head h3{font-family:var(--serif);font-weight:600;font-size:1.25rem}
    .summary__body{padding:.6rem 1.4rem;max-height:300px;overflow-y:auto}
    .sum-empty{padding:2.4rem .5rem;text-align:center;color:var(--faint);font-size:.92rem}
    .sum-empty i{font-size:1.8rem;display:block;margin-bottom:.6rem;opacity:.6}
    .sum-item{display:grid;grid-template-columns:1fr auto auto;align-items:center;gap:.7rem;
      padding:.85rem 0;border-bottom:1px solid var(--line)}
    .sum-item:last-child{border-bottom:none}
    .sum-item__title{font-weight:600;font-size:.92rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .sum-item__price{font-weight:700;color:var(--muted)}
    .sum-item__price::before{content:"$";color:var(--accent)}
    .sum-item__rm{width:26px;height:26px;border-radius:8px;border:1px solid var(--line);
      background:transparent;color:var(--muted);cursor:pointer;font-size:1rem;line-height:1;transition:.2s}
    .sum-item__rm:hover{color:var(--rose);border-color:rgba(255,107,107,.4)}
    .summary__foot{padding:1.3rem 1.4rem;border-top:1px solid var(--line);background:rgba(0,0,0,.15)}
    .total-row{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:1.1rem}
    .total-row span{color:var(--muted);font-weight:600}
    .total-row b{font-family:var(--serif);font-weight:700;font-size:2rem;letter-spacing:-.02em}
    .total-row b::before{content:"$";color:var(--accent);font-size:.6em;vertical-align:.25em;margin-right:2px}
    .checkout-btn{
      width:100%;cursor:pointer;border:none;border-radius:14px;padding:1rem;font-family:var(--sans);
      font-weight:800;font-size:1rem;color:#1a1204;
      background:linear-gradient(120deg,var(--accent),#f0912b);
      display:flex;align-items:center;justify-content:center;gap:.55rem;transition:.25s;
      box-shadow:0 14px 30px -12px rgba(245,165,36,.7)}
    .checkout-btn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 20px 40px -12px rgba(245,165,36,.85)}
    .checkout-btn:disabled{background:var(--surface-3);color:var(--faint);cursor:not-allowed;box-shadow:none}
    .safe-note{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:.9rem;
      color:var(--faint);font-size:.78rem}

    /* ---- Payment modal ---- */
    .modal{position:fixed;inset:0;z-index:50;display:none;align-items:center;justify-content:center;padding:1.2rem}
    .modal.is-open{display:flex}
    .modal__backdrop{position:absolute;inset:0;background:rgba(6,7,10,.72);backdrop-filter:blur(8px);
      animation:fade .3s both}
    .modal__card{position:relative;width:min(480px,100%);max-height:92vh;overflow-y:auto;border-radius:24px;
      background:var(--surface);border:1px solid var(--line-2);box-shadow:var(--shadow);
      animation:pop .38s cubic-bezier(.2,.9,.3,1.1) both}
    .modal__head{padding:1.5rem 1.6rem 1.1rem;position:relative}
    .modal__head .k{color:var(--accent);font-weight:700;text-transform:uppercase;letter-spacing:.15em;font-size:.7rem}
    .modal__head h3{font-family:var(--serif);font-weight:600;font-size:1.6rem;margin-top:.35rem}
    .modal__close{position:absolute;top:1.2rem;right:1.2rem;width:34px;height:34px;border-radius:10px;
      border:1px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;font-size:1.1rem;transition:.2s}
    .modal__close:hover{color:var(--text);border-color:var(--line-2);transform:rotate(90deg)}
    .modal__body{padding:0 1.6rem 1.6rem}

    .pay-sum{border:1px solid var(--line);border-radius:14px;padding:.4rem 1rem;margin-bottom:1.3rem;
      background:rgba(0,0,0,.18);max-height:150px;overflow-y:auto}
    .pay-sum__row{display:flex;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid var(--line);font-size:.9rem}
    .pay-sum__row:last-child{border-bottom:none}
    .pay-sum__row span:first-child{color:var(--muted)}
    .pay-sum__row span:last-child{font-weight:700}
    .pay-total{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:1.4rem}
    .pay-total span{color:var(--muted);font-weight:600}
    .pay-total b{font-family:var(--serif);font-size:1.7rem;font-weight:700}
    .pay-total b::before{content:"$";color:var(--accent);font-size:.6em;vertical-align:.25em}

    /* segmented method toggle */
    .seg{display:grid;grid-template-columns:1fr 1fr;gap:.4rem;background:var(--surface-3);
      padding:.35rem;border-radius:14px;margin-bottom:1.4rem;border:1px solid var(--line)}
    .seg button{display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem;border:none;
      border-radius:10px;background:transparent;color:var(--muted);font-family:var(--sans);font-weight:700;
      font-size:.92rem;cursor:pointer;transition:.22s}
    .seg button i{font-size:1.05rem}
    .seg button.is-active{background:var(--text);color:#14161d}
    .seg button.is-active[data-method="cash"]{background:var(--emerald);color:#053225}

    .fields{display:grid;gap:.9rem;transition:.3s}
    .fields.is-hidden{display:none}
    .field label{display:block;color:var(--muted);font-size:.82rem;font-weight:600;margin-bottom:.4rem}
    .field input{width:100%;background:var(--surface-2);border:1px solid var(--line);border-radius:12px;
      padding:.85rem 1rem;color:var(--text);font-family:var(--sans);font-size:.95rem;transition:.2s}
    .field input::placeholder{color:var(--faint)}
    .field input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(245,165,36,.15)}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:.9rem}
    .cash-note{display:flex;gap:.7rem;padding:1rem 1.1rem;border-radius:14px;margin-bottom:.2rem;
      background:rgba(46,230,166,.08);border:1px solid rgba(46,230,166,.25);color:var(--muted);font-size:.88rem}
    .cash-note i{color:var(--emerald);font-size:1.2rem;flex:none}
    .cash-note.is-hidden{display:none}

    .pay-submit{width:100%;margin-top:1.4rem;cursor:pointer;border:none;border-radius:14px;padding:1rem;
      font-family:var(--sans);font-weight:800;font-size:1rem;color:#1a1204;
      background:linear-gradient(120deg,var(--accent),#f0912b);display:flex;align-items:center;
      justify-content:center;gap:.55rem;transition:.25s}
    .pay-submit:hover{transform:translateY(-2px)}
    .pay-submit.is-cash{background:linear-gradient(120deg,var(--emerald),#1fbf88);color:#053225}

    @keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
    @keyframes fade{from{opacity:0}to{opacity:1}}
    @keyframes pop{from{opacity:0;transform:translateY(20px) scale(.96)}to{opacity:1;transform:none}}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
  </style>
</head>
<body>
  <div class="wrap">

    <!-- Top bar -->
    <header class="topbar">
      <div class="brand"><span class="brand__dot"><i class="bi bi-mortarboard-fill"></i></span> E‑Learning</div>
      <a class="back" href="/student"><i class="bi bi-arrow-left"></i> Back to courses</a>
    </header>

    <!-- Hero -->
    <section class="hero">
      <p class="eyebrow">Secure checkout</p>
      <h1>Complete your <em>enrolment.</em></h1>
      <p>Add the courses you’re ready for, then pay by card or settle in cash — your learning starts the moment you check out.</p>
    </section>

    <?php if (!empty($paid)): ?>
    <div class="paid-banner" id="paid-banner">
      <i class="bi bi-check-circle-fill"></i>
      <div>
        <b>Payment successful</b><br>
        <span>Your courses are unlocked. Head back to start learning.</span>
      </div>
      <button class="close" onclick="document.getElementById('paid-banner').remove()" aria-label="Dismiss">&times;</button>
    </div>
    <?php endif; ?>

    <div class="grid">
      <!-- Course list -->
      <section>
        <div class="panel-label">
          <h2>Available courses</h2>
          <small><?= count($orders ?? []) ?> in your list</small>
        </div>

        <?php if (empty($orders)): ?>
          <div class="empty">
            <i class="bi bi-collection"></i>
            <p>You don’t have any courses waiting to be purchased.</p>
            <a href="/student">Browse courses</a>
          </div>
        <?php else: ?>
          <div class="courses">
            <?php foreach (($orders ?? []) as $i => $order): ?>
              <article class="course" style="animation-delay:<?= 0.05 * $i ?>s">
                <img class="course__img" src="uploading/<?= e($order['image_courses']) ?>" alt="" onerror="this.style.visibility='hidden'">
                <div class="course__body">
                  <h3 class="course__title"><?= e($order['title']) ?></h3>
                  <span class="course__tag"><i class="bi bi-play-circle-fill"></i> Lifetime access</span>
                </div>
                <div class="course__side">
                  <span class="course__price"><?= e($order['price']) ?></span>
                  <button type="button" class="add-btn"
                          data-add="<?= (int) $order['course_id'] ?>"
                          data-title="<?= e($order['title']) ?>"
                          data-price="<?= e($order['price']) ?>">Add</button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Order summary -->
      <aside class="summary">
        <div class="summary__head"><i class="bi bi-bag-heart-fill"></i><h3>Your cart</h3></div>
        <div class="summary__body">
          <div class="sum-empty" id="sum-empty">
            <i class="bi bi-cart"></i>
            Nothing here yet — add a course to begin.
          </div>
          <div id="cart-items"></div>
        </div>
        <div class="summary__foot">
          <div class="total-row"><span>Total</span><b id="cart-total">0.00</b></div>
          <button class="checkout-btn" id="checkout-btn" disabled>
            <i class="bi bi-lock-fill"></i> Proceed to payment
          </button>
          <div class="safe-note"><i class="bi bi-shield-check"></i> Encrypted &amp; secure</div>
        </div>
      </aside>
    </div>
  </div>

  <!-- Payment modal -->
  <div class="modal" id="pay-modal" role="dialog" aria-modal="true" aria-labelledby="pay-title">
    <div class="modal__backdrop" data-close></div>
    <div class="modal__card">
      <div class="modal__head">
        <div class="k">Payment</div>
        <h3 id="pay-title">Checkout</h3>
        <button class="modal__close" data-close aria-label="Close">&times;</button>
      </div>
      <div class="modal__body">
        <div class="pay-sum" id="pay-summary"></div>
        <div class="pay-total"><span>Amount due</span><b id="pay-total">0.00</b></div>

        <div class="seg" role="tablist" aria-label="Payment method">
          <button type="button" data-method="card" class="is-active"><i class="bi bi-credit-card-2-front"></i> Card</button>
          <button type="button" data-method="cash"><i class="bi bi-cash-coin"></i> Cash</button>
        </div>

        <form id="payment-form" action="/orders" method="post">
          <div class="fields" id="card-fields">
            <div class="field">
              <label for="card-holder-name">Cardholder name</label>
              <input type="text" id="card-holder-name" name="card-holder-name" placeholder="Jane Doe" data-req="1" required>
            </div>
            <div class="field">
              <label for="card-number">Card number</label>
              <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" data-req="1" required>
            </div>
            <div class="row2">
              <div class="field">
                <label for="expiration-date">Expiry date</label>
                <input type="date" id="expiration-date" name="expiration-date" data-req="1" required>
              </div>
              <div class="field">
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" placeholder="123" data-req="1" required>
              </div>
            </div>
          </div>

          <div class="cash-note is-hidden" id="cash-note">
            <i class="bi bi-cash-stack"></i>
            <div>Reserve now and pay in person at the campus desk. No card details needed — your seat is held immediately.</div>
          </div>

          <input type="hidden" name="email" value="<?= e($email) ?>">
          <input type="hidden" name="selectioned" id="f-selectioned">
          <input type="hidden" name="totals" id="f-totals">
          <input type="hidden" name="method" id="f-method" value="card">

          <button type="submit" class="pay-submit" id="pay-submit"><i class="bi bi-lock-fill"></i> Pay now</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const money = n => Number(n || 0).toFixed(2);
      const cart = new Map(); // id -> {id,title,price}

      const addBtns   = document.querySelectorAll('.add-btn');
      const cartItems = document.getElementById('cart-items');
      const sumEmpty  = document.getElementById('sum-empty');
      const totalEl   = document.getElementById('cart-total');
      const checkout  = document.getElementById('checkout-btn');

      function render(){
        cartItems.innerHTML = '';
        let total = 0;
        cart.forEach(c => {
          total += Number(c.price);
          const row = document.createElement('div'); row.className = 'sum-item';
          const t = document.createElement('span'); t.className = 'sum-item__title'; t.textContent = c.title;
          const p = document.createElement('span'); p.className = 'sum-item__price'; p.textContent = money(c.price);
          const x = document.createElement('button'); x.className = 'sum-item__rm'; x.type='button';
          x.dataset.rm = c.id; x.setAttribute('aria-label','Remove'); x.innerHTML = '&times;';
          row.append(t, p, x); cartItems.appendChild(row);
        });
        sumEmpty.style.display = cart.size ? 'none' : 'block';
        totalEl.textContent = money(total);
        checkout.disabled = cart.size === 0;
        addBtns.forEach(b => {
          const on = cart.has(b.dataset.add);
          b.classList.toggle('is-added', on);
          b.textContent = on ? 'Added' : 'Add';
        });
      }

      addBtns.forEach(b => b.addEventListener('click', () => {
        const id = b.dataset.add;
        if (cart.has(id)) cart.delete(id);
        else cart.set(id, {id, title:b.dataset.title, price:b.dataset.price});
        render();
      }));
      cartItems.addEventListener('click', e => {
        const rm = e.target.closest('[data-rm]');
        if (rm){ cart.delete(rm.dataset.rm); render(); }
      });

      /* ---- Modal ---- */
      const modal = document.getElementById('pay-modal');
      const paySummary = document.getElementById('pay-summary');
      const payTotal = document.getElementById('pay-total');
      const fSel = document.getElementById('f-selectioned');
      const fTot = document.getElementById('f-totals');

      function openModal(){
        paySummary.innerHTML = '';
        let total = 0; const ids = [];
        cart.forEach(c => {
          total += Number(c.price); ids.push(c.id);
          const row = document.createElement('div'); row.className='pay-sum__row';
          const a = document.createElement('span'); a.textContent = c.title;
          const b = document.createElement('span'); b.textContent = '$' + money(c.price);
          row.append(a, b); paySummary.appendChild(row);
        });
        payTotal.textContent = money(total);
        fSel.value = ids.join(',');
        fTot.value = money(total);
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      }
      function closeModal(){ modal.classList.remove('is-open'); document.body.style.overflow = ''; }
      checkout.addEventListener('click', () => { if (cart.size) openModal(); });
      modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeModal));
      document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

      /* ---- Method toggle ---- */
      const methodInput = document.getElementById('f-method');
      const cardFields  = document.getElementById('card-fields');
      const cashNote    = document.getElementById('cash-note');
      const paySubmit   = document.getElementById('pay-submit');
      const cardInputs  = cardFields.querySelectorAll('input');

      document.querySelectorAll('.seg button').forEach(btn => btn.addEventListener('click', () => {
        document.querySelectorAll('.seg button').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
        const m = btn.dataset.method;
        methodInput.value = m;
        if (m === 'cash'){
          cardFields.classList.add('is-hidden');
          cashNote.classList.remove('is-hidden');
          cardInputs.forEach(i => i.required = false);
          paySubmit.classList.add('is-cash');
          paySubmit.innerHTML = '<i class="bi bi-cash-coin"></i> Confirm cash payment';
        } else {
          cardFields.classList.remove('is-hidden');
          cashNote.classList.add('is-hidden');
          cardInputs.forEach(i => i.required = i.dataset.req === '1');
          paySubmit.classList.remove('is-cash');
          paySubmit.innerHTML = '<i class="bi bi-lock-fill"></i> Pay now';
        }
      }));

      render();
    })();
  </script>
</body>
</html>
