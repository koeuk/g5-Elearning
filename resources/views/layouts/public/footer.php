
<!-- =======================
Footer START -->
<footer class="pt-5">
	<div class="container">
		<!-- Row START -->
		<div class="row g-4">

			<!-- Widget 1 START -->
			<div class="col-lg-3">
				<!-- logo -->
				<a class="me-0" href="index.html">
					<img class="light-mode-item h-40px" src="assets/images/logo.svg" alt="logo">
					<img class="dark-mode-item h-40px" src="assets/images/logo-light.svg" alt="logo">
				</a>
				<p class="my-3">Eduport education theme, built specifically for the education centers which is dedicated to teaching and involve learners.</p>
				<!-- Social media icon -->
				<ul class="list-inline mb-0 mt-3">
					<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-facebook" href="#"><i class="fab fa-fw fa-facebook-f"></i></a> </li>
					<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-instagram" href="#"><i class="fab fa-fw fa-instagram"></i></a> </li>
					<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-twitter" href="#"><i class="fab fa-fw fa-twitter"></i></a> </li>
					<li class="list-inline-item"> <a class="btn btn-white btn-sm shadow px-2 text-linkedin" href="#"><i class="fab fa-fw fa-linkedin-in"></i></a> </li>
				</ul>
			</div>
			<!-- Widget 1 END -->

			<!-- Widget 2 START -->
			<div class="col-lg-6">
				<div class="row g-4">
			
					<!-- Link block -->
					<div class="col-6 col-md-4">
						<h5 class="mb-2 mb-md-4">Community</h5>
						<ul class="nav flex-column">
							<li class="nav-item"><a class="nav-link" href="#">Video</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Quiz</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Lesson</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Study</a></li>
						</ul>
					</div>

					<!-- Link block -->
					<div class="col-6 col-md-4">
						<h5 class="mb-2 mb-md-4">Teaching</h5>
						<ul class="nav flex-column">
							<li class="nav-item"><a class="nav-link" href="#">Basic study</a></li>
							<li class="nav-item"><a class="nav-link" href="#">How to guide</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Terms &amp; Conditions</a></li>
						</ul>
					</div>

					<!-- Link block -->
					<div class="col-6 col-md-4">
						<h5 class="mb-2 mb-md-4">Advantage</h5>
						<ul class="nav flex-column">
							<li class="nav-item"><a class="nav-link" href="#">Faster</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Easy</a></li>
							<li class="nav-item"><a class="nav-link" href="#">Don't spend much time</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- Widget 2 END -->

			<!-- Widget 3 START -->
			<div class="col-lg-3">
				<h5 class="mb-2 mb-md-4">Contact</h5>
				<!-- Time -->
				<p class="mb-2">
					Toll free:<span class="h6 fw-light ms-2">+1234 568 963</span>
					<span class="d-block small">(9:AM to 8:PM IST)</span>
				</p>

				<p class="mb-0">Email:<span class="h6 fw-light ms-2">eduport.team@gmail.com</span></p>

				<div class="row g-2 mt-2">
					<!-- Google play store button -->
					<div class="col-6 col-sm-4 col-md-3 col-lg-6">
						<a href="#"> <img src="assets/images/client/google-play.svg" alt=""> </a>
					</div>
					<!-- App store button -->
					<div class="col-6 col-sm-4 col-md-3 col-lg-6">
						<a href="#"> <img src="assets/images/client/app-store.svg" alt="app-store"> </a>
					</div>
				</div> <!-- Row END -->
			</div> 
			<!-- Widget 3 END -->
		</div><!-- Row END -->

		<!-- Divider -->
		<hr class="mt-4 mb-0">

		<!-- Bottom footer -->
		<div class="py-3">
			<div class="container px-0">
				<div class="d-md-flex justify-content-between align-items-center py-3 text-center text-md-left">
					<!-- copyright text -->
					<div class="text-primary-hover"> We are here <a href="#" class="text-body">to support Eduport</a></div>
					<!-- copyright links-->
					<div class=" mt-3 mt-md-0">
						<ul class="list-inline mb-0">
							<li class="list-inline-item"><a class="nav-link" href="#">Terms of use</a></li>
							<li class="list-inline-item"><a class="nav-link pe-0" href="#">Privacy policy</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- =======================
Footer END -->

<!-- Back to top -->
<div class="back-top"><i class="bi bi-arrow-up-short position-absolute top-50 start-50 translate-middle"></i></div>

<!-- Bootstrap JS -->
<script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Vendors -->
<script src="vendor/tiny-slider/tiny-slider.js"></script>
<script src="vendor/purecounterjs/dist/purecounter_vanilla.js"></script>

<!-- Template Functions -->
<script src="vendor/js/functions.js"></script>

<!-- ===================== Guest login popup ===================== -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 px-sm-5 pb-5 pt-0 text-center">
        <div class="mb-3">
          <span class="d-inline-grid" style="width:58px;height:58px;border-radius:16px;background:rgba(242,133,0,.12);color:#F28500;place-items:center;font-size:1.6rem;">
            <i class="fas fa-graduation-cap"></i>
          </span>
        </div>
        <h3 class="fw-bold mb-1" id="loginModalLabel">Sign in to continue</h3>
        <p class="text-muted mb-4">Log in to enrol, add courses to your cart and start learning.</p>

        <form action="/access" method="post" class="text-start">
          <div class="mb-3">
            <label class="form-label small fw-semibold text-dark">Email address</label>
            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="name@example.com" required autocomplete="email">
          </div>
          <div class="mb-4">
            <label class="form-label small fw-semibold text-dark">Password</label>
            <div class="input-group">
              <input type="password" name="password" id="loginModalPw" class="form-control form-control-lg bg-light border-0" placeholder="••••••••" required autocomplete="current-password">
              <button class="btn bg-light border-0" type="button" id="loginModalEye" aria-label="Show password"><i class="bi bi-eye text-muted"></i></button>
            </div>
          </div>
          <button type="submit" class="btn btn-lg w-100 text-white fw-semibold" style="background:#F28500;">
            <i class="fas fa-sign-in-alt me-2"></i>Sign in
          </button>
        </form>

        <p class="mt-3 mb-0 small text-muted">Don't have an account?
          <a href="/signup" class="fw-semibold" style="color:#F28500;">Create one</a>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- ===================== Guest sign-up popup ===================== -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 px-sm-5 pb-5 pt-0 text-center">
        <div class="mb-3">
          <span class="d-inline-grid" style="width:58px;height:58px;border-radius:16px;background:rgba(242,133,0,.12);color:#F28500;place-items:center;font-size:1.6rem;">
            <i class="fas fa-user-plus"></i>
          </span>
        </div>
        <h3 class="fw-bold mb-1" id="signupModalLabel">Create your account</h3>
        <p class="text-muted mb-4">Free to join — start learning in minutes.</p>

        <form action="/create_student" method="post" enctype="multipart/form-data" class="text-start">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label small fw-semibold text-dark">Full name</label>
              <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Jane Doe" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label small fw-semibold text-dark">Phone</label>
              <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" placeholder="0XX XXX XXX">
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label small fw-semibold text-dark">Email address</label>
            <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="name@example.com" required>
          </div>
          <div class="row g-3 mt-0">
            <div class="col-sm-6">
              <label class="form-label small fw-semibold text-dark">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="signupModalPw" class="form-control form-control-lg bg-light border-0" placeholder="••••••••" required>
                <button class="btn bg-light border-0" type="button" id="signupModalEye" aria-label="Show password"><i class="bi bi-eye text-muted"></i></button>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label small fw-semibold text-dark">Confirm password</label>
              <input type="password" name="password_comfirm" class="form-control form-control-lg bg-light border-0" placeholder="••••••••" required>
            </div>
          </div>
          <div class="d-flex align-items-center gap-4 mt-3">
            <span class="small fw-semibold text-dark">Gender:</span>
            <div class="form-check mb-0"><input class="form-check-input" type="radio" name="gender" value="Male" id="suMale" checked><label class="form-check-label" for="suMale">Male</label></div>
            <div class="form-check mb-0"><input class="form-check-input" type="radio" name="gender" value="Female" id="suFemale"><label class="form-check-label" for="suFemale">Female</label></div>
          </div>
          <div class="mt-3">
            <label class="form-label small fw-semibold text-dark">Profile photo <span class="text-muted fw-normal">(optional)</span></label>
            <input type="file" name="image" accept="image/*" class="form-control bg-light border-0">
          </div>
          <button type="submit" class="btn btn-lg w-100 text-white fw-semibold mt-4" style="background:#F28500;">
            <i class="fas fa-user-plus me-2"></i>Create account
          </button>
        </form>

        <p class="mt-3 mb-0 small text-muted">Already have an account?
          <a href="/signin" class="fw-semibold" style="color:#F28500;">Sign in</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
  // Guests: intercept "sign in" / "create account" links and show the matching
  // popup instead of navigating to the full page. Switching between the two
  // waits for one to close before opening the other (no stacked backdrops).
  function eLearnAuthOpen(which) {
    var other = which === 'loginModal' ? 'signupModal' : 'loginModal';
    var otherEl = document.getElementById(other);
    var target = document.getElementById(which);
    if (otherEl && otherEl.classList.contains('show')) {
      otherEl.addEventListener('hidden.bs.modal', function h() {
        otherEl.removeEventListener('hidden.bs.modal', h);
        bootstrap.Modal.getOrCreateInstance(target).show();
      });
      bootstrap.Modal.getOrCreateInstance(otherEl).hide();
    } else {
      bootstrap.Modal.getOrCreateInstance(target).show();
    }
  }
  document.addEventListener('click', function (e) {
    var signin = e.target.closest('a[href="/signin"], a[href="signin"]');
    var signup = e.target.closest('a[href="/signup"], a[href="signup"], a[href="/create_student"]');
    if (signin) { e.preventDefault(); eLearnAuthOpen('loginModal'); }
    else if (signup) { e.preventDefault(); eLearnAuthOpen('signupModal'); }
  });
  // Show/hide password toggles inside the popups.
  [['loginModalPw', 'loginModalEye'], ['signupModalPw', 'signupModalEye']].forEach(function (pair) {
    var pw = document.getElementById(pair[0]), eye = document.getElementById(pair[1]);
    if (!pw || !eye) return;
    eye.addEventListener('click', function () {
      var show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      eye.querySelector('i').className = (show ? 'bi bi-eye-slash' : 'bi bi-eye') + ' text-muted';
    });
  });
</script>

</body>
</html>