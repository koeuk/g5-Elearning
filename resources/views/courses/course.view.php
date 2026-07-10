<?php
/**
 * Student course list for one category.
 *
 * @var array $student    logged-in student (email, user_id …)
 * @var array $category   the chosen category (title …)
 * @var int   $categoryId the chosen category id
 * @var array $courses    courses in the category, each with paid/in_cart flags
 */

use App\Core\View;

$student    = $student ?? [];
$category   = $category ?? [];
$categoryId = $categoryId ?? 0;
$courses    = $courses ?? [];
$email      = $student['email'] ?? '';

echo View::partial('layouts/public/header');
?>

<!-- **************** MAIN CONTENT START **************** -->
<main>
<style>
	.min-w-0 { min-width: 0; }
	.course-card { transition: transform .25s ease, box-shadow .25s ease; }
	.course-card:hover { transform: translateY(-4px); box-shadow: 0 1rem 2rem rgba(0,0,0,.12) !important; }
	.course-card .card-title a { overflow-wrap: break-word; }
	.course-card__img { position: relative; }
	.icon-lg { width: 60px; height: 60px; display: grid; place-items: center; font-size: 1.4rem; }
</style>
<!-- Inner part START -->
<section class="position-relative d-flex align-items-center"
	style="min-height: 320px; background-image: linear-gradient(rgba(255,255,255,.55), rgba(255,255,255,.9)), url('assets/images/bg/abstract-1264071_1280.jpg'); background-size: cover; background-position: center;">
	<div class="container py-5">
		<a href="/student" class="btn btn-orange btn-sm mb-4">
			<i class="bi bi-arrow-left-circle-fill"></i> Back
		</a>
		<div class="row">
			<div class="col-lg-9 mx-auto text-center">
				<h1 class="fs-1 fw-bold mb-2">Welcome to the <span class='text-orange'><?= e($category['title'] ?? '') ?></span> Category</h1>
				<p class="lead text-secondary mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
			</div>
		</div>
	</div>
</section>
<section class="py-5" id='c' style="background-color: rgba(0, 0, 0,0.04);">
	<div class="container">
		<!-- Course list START -->
		<?php if (count($courses) < 1): ?>
			<div class="text-center py-5">
				<div class="icon-lg bg-orange bg-opacity-10 text-orange rounded-circle mx-auto mb-3"><i class="fas fa-book-open"></i></div>
				<h3 class="text-orange">No courses here yet</h3>
				<p class="text-muted mb-0">Check back soon — new courses are added to this category regularly.</p>
			</div>
		<?php endif; ?>
		<div class="row g-4 justify-content-center">
		<?php foreach ($courses as $course): ?>
			<!-- Card item START -->
			<div class="col-12 col-lg-6">
				<div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden course-card">
					<div class="row g-0 h-100">
						<!-- Image -->
						<div class="col-4 bg-light course-card__img"
							style="min-height: 190px; background-image: url('uploading/<?= e($course['image_courses']) ?>'); background-size: cover; background-position: center;">
						</div>
						<!-- Card body -->
						<div class="col-8">
							<div class="card-body d-flex flex-column h-100 p-3 p-lg-4">
								<!-- Title + price -->
								<div class="d-flex justify-content-between align-items-start gap-2 mb-2">
									<div class="min-w-0">
										<h5 class="card-title mb-1 lh-sm text-dark"><?= e($course['title']) ?></h5>
										<p class="small text-muted mb-0"><i class="fas fa-chalkboard-teacher text-orange me-1"></i>Professor at Sigma College</p>
									</div>
									<h5 class="text-orange fw-bold mb-0 flex-shrink-0" <?php if ($course['paid']) { echo 'hidden'; } ?>>$<?= e($course['price']) ?></h5>
									<form action="/blog_learning" method='post' class="flex-shrink-0" <?php if (!$course['paid']) { echo 'hidden'; } ?>>
										<input type="text" value='<?= e($email) ?>' name='email' hidden>
										<input type="text" value='<?= e($course['course_id']) ?>' name='course_id' hidden>
										<input type="text" value='<?= e($categoryId) ?>' name='id' hidden>
										<button type="submit" class="btn btn-primary btn-sm">Join course</button>
									</form>
								</div>
								<!-- Content -->
								<p class="text-truncate-2 small text-muted mb-3"><?= e($course['description']) ?></p>
								<!-- Info footer (pinned to bottom for equal cards) -->
								<div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
									<span class="badge rounded-pill bg-info bg-opacity-10 text-info fw-semibold px-3 py-2">Digital Marketing</span>
									<div class="d-flex align-items-center gap-3">
										<span class="d-flex align-items-center" title="Enrolled students">
											<span class="icon-md bg-orange bg-opacity-10 text-orange rounded-circle"><i class="fas fa-user-graduate"></i></span>
											<span class="h6 fw-light mb-0 ms-2">9.1k</span>
										</span>
										<button class="icon-md bg-white rounded-circle border border-orange text-orange show-popup" data-bs-toggle="modal" data-bs-target="#paymentModal" data-user='<?= e($email) ?>' data-course='<?= e($course['course_id']) ?>' data-title="<?= e($course['title']) ?>" data-id="<?= e($categoryId) ?>" data-price="<?= e($course['price']) ?>" data-imgs='<?= e($course['image_courses']) ?>' title="Add to cart" <?php if ($course['paid'] || $course['in_cart']) { echo 'hidden'; } ?>><i class="fas fa-shopping-cart text-danger"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Card item END -->
		<?php endforeach; ?>
		<!-- Course list END -->
		</div>
	</div>
</section>
<!-- Inner part END -->

<!-- Action box START -->
<section class="pt-0">
	<div class="container position-relative">
		<div class="bg-orange p-4 p-sm-5 rounded-3">
			<div class="row justify-content-center position-relative">
				<div class="col-11 position-relative">
					<div class="row align-items-center">
						<div class="col-lg-7">
							<h3 class="text-white">Welcome to our best learner!</h3>
							<p class="text-white mb-3 mb-lg-0">Speedily say has suitable disposal add boy. On forth doubt miles of child. Exercise joy man children rejoiced. Yet uncommonly his ten who diminution astonished.</p>
						</div>
						<div class="col-lg-5 text-lg-end">
							<a href="/student" class="btn btn-dark mb-0">Browse all courses</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Payment quick-view modal -->
<div class="container mt-5">
	<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body border p-4 m-4">
					<div class="text-center">
						<img src="" alt="Course image" id='imgs' class="rounded-circle mb-3" style="width: 130px; height: 130px; object-fit: cover;">
					</div>
					<div class="text-center">Course:<h5 class="text-info" id="modalTitle"></h5></div>
					<div class="text-center">Price:<h5 class="text-success" id="modalPrice"></h5></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<form action='/course#c' method='post'>
							<input type="text" id="modalUser" value='<?= e($email) ?>' name='email' hidden>
							<input type="text" id="modalCourse" value='' name='course_id' hidden>
							<input type="text" value='<?= e($categoryId) ?>' name='id' hidden>
							<button type='submit' class="btn btn-primary succeses-popup">Add to card</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Action box END -->

<?= View::render('students/payments/payment', ['email' => $email]) ?>
</main>
<!-- **************** MAIN CONTENT END **************** -->

<!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
  $('.show-popup').click(function() {
    var title  = $(this).data('title');
    var price  = $(this).data('price');
    var user   = $(this).data('user');
    var course = $(this).data('course');
    var imgs   = $(this).data('imgs');

    $('#modalTitle').text(title);
    $('#modalPrice').text(price);
    $('#modalUser').val(user);
    $('#modalCourse').val(course);
    $('#imgs').attr('src', 'uploading/' + imgs);
    $('#paymentModal').modal('show');
  });
});
</script>

<?= View::partial('layouts/public/footer') ?>
