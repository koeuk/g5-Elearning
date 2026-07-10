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
<!-- Inner part START -->
<section style='height: 300px;background-image: url("assets/images/bg/abstract-1264071_1280.jpg");'>
	<div class="mt-0">
		<a href="/student" class="btn btn-orange btn-sm">
			<i class="bi bi-arrow-left-circle-fill"></i> Back
		</a>
	</div>
	<div class="row mb-4">
		<div class="col-lg-8 text-center mx-auto">
			<h2 class="fs-1">Welcome to the <span class='text-orange'><?= e($category['title'] ?? '') ?></span> Category</h2>
			<p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
		</div>
	</div>
</section>
<section class="pt-4" id='c' style="background-color: rgba(0, 0, 0,0.05);">
	<div class="container">
		<!-- Course list START -->
		<div class="row g-4 justify-content-center">
		<?php if (count($courses) < 1): ?>
			<h3 class="text-center text-orange">The Course did not add yet!</h3>
		<?php endif; ?>
		<?php foreach ($courses as $course): ?>
			<!-- Card item START -->
			<div class="col-lg-10 col-xl-6">
				<div class="card shadow p-2">
					<div class="row g-0">
						<!-- Image -->
						<div class="col-md-4 rounded-4" style="background-image: url('uploading/<?= e($course['image_courses']) ?>'); background-size: cover;">
						</div>
						<!-- Card body -->
						<div class="col-md-8">
							<div class="card-body">
								<!-- Title -->
								<div class="d-sm-flex justify-content-sm-between mb-2 mb-sm-3">
									<div>
										<h5 class="card-title mb-0"><a href="/trainer-classroom"><?= e($course['title']) ?></a></h5>
										<p class="small mb-2 mb-sm-0">Professor at Sigma College</p>
									</div>
									<h5 class="text-orange mb-0" <?php if ($course['paid']) { echo 'hidden'; } ?>><?= e($course['price']) ?></h5>
									<form action="/blog_learning" method='post' <?php if (!$course['paid']) { echo 'hidden'; } ?>>
										<input type="text" value='<?= e($email) ?>' name='email' hidden>
										<input type="text" value='<?= e($course['course_id']) ?>' name='course_id' hidden>
										<input type="text" value='<?= e($categoryId) ?>' name='id' hidden>
										<button type="submit" class="btn btn-primary">Join course</button>
									</form>
								</div>
								<!-- Content -->
								<p class="text-truncate-2 mb-3"><?= e($course['description']) ?></p>
								<!-- Info -->
								<div class="d-sm-flex justify-content-sm-between align-items-center">
									<h6 class="text-info mb-0">Digital Marketing</h6>
									<li class="list-inline-item d-flex justify-content-center align-items-center">
										<div class="icon-md bg-orange bg-opacity-10 text-orange rounded-circle"><i class="fas fa-user-graduate"></i></div>
										<span class="h6 fw-light mb-0 ms-2">9.1k</span>
									</li>
									<button class="icon-md bg-white rounded-circle border border-orange text-orange show-popup" data-bs-toggle="modal" data-bs-target="#paymentModal" data-user='<?= e($email) ?>' data-course='<?= e($course['course_id']) ?>' data-title="<?= e($course['title']) ?>" data-id="<?= e($categoryId) ?>" data-price="<?= e($course['price']) ?>" data-imgs='<?= e($course['image_courses']) ?>' <?php if ($course['paid'] || $course['in_cart']) { echo 'hidden'; } ?>><i class="fas fa-shopping-cart text-danger"></i></button>
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
