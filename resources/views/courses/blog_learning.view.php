<?php
/**
 * Student classroom for a purchased course.
 *
 * @var array $student  logged-in student (email …)
 * @var array $course   the course being studied (title, user_id …)
 * @var int   $courseId the course id
 * @var array $lessons  lessons for the course, each with a `quizzes` list
 * @var array $teacher  the course's trainer (name, email, phone, profile_image)
 */

use App\Core\View;

$student  = $student ?? [];
$course   = $course ?? [];
$courseId = $courseId ?? 0;
$lessons  = $lessons ?? [];
$teacher  = $teacher ?? [];
$email    = $student['email'] ?? '';

echo View::partial('layouts/public/header');
?>
<!-- **************** MAIN CONTENT START **************** -->
<main>
<section id="cover" style='height: 250px; background-size:cover; background-image: url("assets/images/bg/composition-3288397_1280.jpg");'>
	<div class="mt-0">
		<a href="/student" class="btn btn-orange btn-sm">
			<i class="bi bi-arrow-left-circle-fill"></i> Back
		</a>
	</div>
	<div class="row mb-4">
		<div class="col-lg-8 text-center mx-auto">
			<h2 class="fs-1">Welcome to the <span class='text-orange'><?= e($course['title'] ?? '') ?></span> Course</h2>
			<p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
		</div>
	</div>
</section>
<div class="container mt-2 ml-5">
	<button type="button" id='lessons' class="btn btn-outline-orange">Lessons</button>
	<button type="button" id='quizzes' class="btn btn-outline-orange">Quizzes</button>
	<button type="button" id='trainers' class="btn btn-outline-orange">Trainer</button>
</div>

<!-- Lessons START -->
<section class="bg-light position-relative" id='blog_study' style="background-color: rgba(0, 0, 0,0.05);">
	<div class="container">
		<div class="row g-4 align-items-center justify-content-between">
			<div class="col-md-6 col-xl-4">
				<h2 class="fs-1">Here are the Lessons</h2>
				<p>Work through each lesson at your own pace. Watch the video, then test your understanding in the Quizzes tab.</p>
			</div>
			<div class="col-md-6 col-xl-8">
				<div class="row">
					<div class="tiny-slider arrow-round arrow-blur">
						<div class="tiny-slider-inner" data-autoplay="false" data-edge="2" data-arrow="true" data-dots="false" data-items-lg="1" data-items-xl="2">
						<?php if (count($lessons) < 1): ?>
							<p class="text-muted">No lessons have been added to this course yet.</p>
						<?php endif; ?>
						<?php foreach ($lessons as $lesson): ?>
							<!-- Card START -->
							<div class="card p-2">
								<div class="position-relative">
									<iframe width="340" height="250" src="<?= e($lesson['video']) ?>"></iframe>
									<div class="card-img-overlay">
										<div class="position-absolute top-50 start-50 translate-middle">
											<a href="<?= e($lesson['video']) ?>" class="btn btn-lg text-danger btn-round btn-white-shadow mb-0" data-glightbox="" data-gallery="video-tour">
												<i class="fas fa-play"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="card-body">
									<h5><?= e($lesson['title']) ?></h5>
									<div class="d-sm-flex justify-content-sm-between align-items-center mt-3">
										<div>
											<h6 class="mb-1 fw-normal"><i class="fas fa-circle fw-bold text-success small me-2"></i></h6>
										</div>
										<button type='button' class="btn btn-sm btn-success mb-0 vdo text-white" data-bs-toggle="modal" data-bs-target="#videoModel" data-videos="<?= e($lesson['video']) ?>" data-lessontitle="<?= e($lesson['title']) ?>" data-description="<?= e($lesson['description']) ?>">Let's study</button>
									</div>
								</div>
							</div>
							<!-- Card END -->
						<?php endforeach ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Quizzes START -->
<section id='testing_blog' style="background-color: rgba(0, 0, 0,0.05);">
	<div class="row mb-4">
		<div class="col-lg-8 text-center mx-auto">
			<h2 class="fs-1">Test your understanding</h2>
			<p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
			<span class='text-orange'>Screenshot your result and upload here</span>
			<form action="/blog_learning#testing_blog" method='post' enctype="multipart/form-data">
				<div class='d-flex gap-2 justify-content-center'>
					<input type="file" name='image' class="form-control" style="background-color: rgba(0, 0, 0, 0.1);">
					<select class="form-select form-select-lg text-center" style="width: 200px; font-size: smaller;" name='lesson_select'>
						<option selected>Select the lesson</option>
						<?php foreach ($lessons as $lesson): ?>
							<?php if (count($lesson['quizzes']) > 0): ?>
							<option value="<?= e($lesson['title']) ?>"><?= e($lesson['title']) ?></option>
							<?php endif; ?>
						<?php endforeach ?>
					</select>
					<input type="text" value='<?= e($courseId) ?>' name='course_id' hidden>
					<button type="submit" class="btn btn-orange d-flex justify-content-center">Submit</button>
				</div>
			</form>
		</div>
	</div>
	<div class="container" style="background-color: rgba(0, 0, 0,0.05);">
		<div class="row g-4">
		<?php foreach ($lessons as $lesson): ?>
			<?php foreach ($lesson['quizzes'] as $quiz): ?>
			<!-- Quiz item -->
			<div class="col-sm-6 col-lg-4 col-xl-3">
				<div class="card card-body shadow rounded-3">
					<div class="d-flex align-items-center">
						<img class="rounded-circle me-lg-2" src="assets/images/test.png" alt="" style="width: 70px; height: 70px;">
						<div class="ms-3">
							<button type='button' class="btn btn-outline-none show-quiz" data-bs-toggle="modal" data-bs-target="#paymentModal" data-contents='<?= e($quiz['content']) ?>'>
								<h5 class="mb-0"><?= e($lesson['title']) ?></h5>
								<span>Quiz</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach ?>
		<?php endforeach ?>
		</div>
	</div>
</section>

<!-- Trainer START -->
<section class="pt-4" id="trainer" style="background-color: rgba(0, 0, 0,0.05);">
	<div class="container">
		<div class="col-lg-8 text-center mx-auto">
			<h2 class="fs-1">Trainer</h2>
			<p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
		</div>
		<div class="row align-items-center border rounded p-4 bg-orange shadow">
			<div class="col-lg-4 text-center">
				<img class="img-fluid rounded-circle mb-1" src="uploading/<?= e($teacher['profile_image'] ?? '') ?>" alt="" style="width: 150px; height: 150px; object-fit: cover">
				<h4 class="m-1"><span class="text-white"><?= e($teacher['name'] ?? 'Not assigned') ?></span></h4>
			</div>
			<div class="col-lg-7 py-5 px-3">
				<div class="py-2">
					<div class="col-6">
						<div class="d-flex align-items-center mb-4">
							<i class="fas fa-phone text-white me-2"></i>
							<h5 class="m-0"><span class="text-white">Phone:</span> <?= e($teacher['phone'] ?? '-') ?></h5>
						</div>
					</div>
					<div class="col-6">
						<div class="d-flex align-items-center mb-4">
							<i class="fas fa-envelope text-white me-2"></i>
							<h5 class="text-truncate m-0"><span class="text-white">Email:</span> <?= e($teacher['email'] ?? '-') ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Quiz start modal -->
<div class="container mt-5">
	<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body p-4 m-4">
					<div class="text-center">
						<img src="assets/images/quiz.png" alt="Quiz" class="mb-3" style="width: 180px; height: 70px; object-fit: cover;">
					</div>
					<span class='d-flex justify-content-center'>Screenshot your result and submit</span>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button id='startquiz' class="btn btn-primary success-popup">Start Quiz</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Video modal -->
<div class="container">
	<div class="modal fade" id="videoModel" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="row modal-body p-2 m-4 d-flex justify-content-center">
					<iframe width="1000" height="450" id="vid" src=""></iframe>
					<h5 id='lessontitle' class='m-2'></h5>
				</div>
				<div>
					<p class='d-flex justify-content-center' id='des'></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Quiz content modal -->
<div class="container mt-1 lg-8">
	<div class="modal fade" id="quizModel" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="row modal-body p-2 m-4 d-flex justify-content-center">
					<iframe width="730" height="345" id="quizz" src=""></iframe>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
	$('.vdo').click(function() {
		$('#vid').attr('src', $(this).data('videos'));
		$('#lessontitle').text($(this).data('lessontitle'));
		$('#des').text($(this).data('description'));
	});

	$('.show-quiz').click(function() {
		var contents = $(this).data('contents');
		$('#paymentModal').modal('show');
		$('#startquiz').off('click').on('click', function() {
			$('#paymentModal').modal('hide');
			$('#quizModel').modal('show');
			$('#quizz').attr('src', contents);
		});
	});
});

document.getElementById('lessons').addEventListener('click', function() {
	document.getElementById('testing_blog').style.display = 'none';
	document.getElementById('blog_study').style.display = 'block';
	document.getElementById('trainer').style.display = 'none';
});
document.getElementById('quizzes').addEventListener('click', function() {
	document.getElementById('testing_blog').style.display = 'block';
	document.getElementById('blog_study').style.display = 'none';
	document.getElementById('trainer').style.display = 'none';
});
document.getElementById('trainers').addEventListener('click', function() {
	document.getElementById('testing_blog').style.display = 'none';
	document.getElementById('blog_study').style.display = 'none';
	document.getElementById('trainer').style.display = 'block';
});
</script>
</main>
<!-- **************** MAIN CONTENT END **************** -->

<?= View::partial('layouts/public/footer') ?>
