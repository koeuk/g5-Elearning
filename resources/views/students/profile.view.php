<?php
/**
 * Student profile page.
 *
 * @var array $student the profile owner (name, email, phone, profile_image, user_id)
 * @var array $courses full course rows the student has purchased
 * @var bool  $isAdmin whether an admin is viewing the profile
 */

use App\Core\View;

$student = $student ?? [];
$courses = $courses ?? [];
$isAdmin = $isAdmin ?? false;
$backUrl = $isAdmin ? '/list_student' : '/student';

echo View::partial('layouts/public/header');
?>

<section id="cover" style='height: 250px; background-size:cover; background-image: url("assets/images/bg/composition-3288397_1280.jpg");'>
	<div>
		<a href="<?= $backUrl ?>" class="btn btn-orange btn-sm">
			<i class="bi bi-arrow-left-circle-fill"></i> Back
		</a>
	</div>
	<div class="row mb-4">
		<div class="col-lg-8 text-center mx-auto"></div>
	</div>
</section>
<div style="background-color: rgba(0, 0, 0,0.05);">
<div class="d-flex justify-content-center">
	<section class="bg-white" id="personals">
		<div class="container">
			<div class="row shadow align-items-center border rounded p-4" style="width:600px;margin-top: -57px;height:450px">
				<div class="text-center">
					<img class="img-fluid rounded-circle mb-3" src="uploading/<?= e($student['profile_image'] ?? '') ?>" alt="" style="width: 200px; height: 200px; object-fit: cover; margin-top: -150px;">
					<h4 class="m-1"><span><?= e($student['name'] ?? '') ?></span></h4>
				</div>
				<div class="col-lg-7 py-5 px-3">
					<div class="py-2">
						<div class="col-6">
							<div class="d-flex align-items-center mb-4 gap-2">
								<i class="fas fa-phone text-orange"></i>
								<span>Phone:</span>
								<h6 class="m-0"><?= e($student['phone'] ?? '') ?></h6>
							</div>
						</div>
						<div class="col-6">
							<div class="d-flex align-items-center mb-4 gap-2">
								<i class="fas fa-envelope text-orange"></i>
								<span>Email:</span>
								<h6 class="m-0"><?= e($student['email'] ?? '') ?></h6>
							</div>
						</div>
						<div class="col-6">
							<div class="d-flex align-items-center mb-4 gap-2">
								<i class="fas fa-book text-orange"></i>
								<span>Courses:</span>
								<h6 class="m-0"><?= count($courses) ?> Courses</h6>
							</div>
						</div>
						<div class="col-6">
							<div class="d-flex align-items-center mb-4 gap-2">
								<i class="fas fa-calendar text-orange"></i>
								<span>Joined:</span>
								<h6 class="m-0">2024-03-22</h6>
							</div>
						</div>
					</div>
				</div>
				<div class="d-flex justify-content-end" style="margin-top: -57px;">
					<?php if (!$isAdmin): ?>
					<a href="/student_password" class="btn btn-0"><i class="fas fa-key me-2 text-orange"></i>Change Password</a>
					<button type="button" class="btn border-0 show-edit" data-bs-toggle="modal" data-bs-target="#editPersonalModal"
						data-image="<?= e($student['profile_image'] ?? '') ?>" data-name="<?= e($student['name'] ?? '') ?>"
						data-phone="<?= e($student['phone'] ?? '') ?>" data-email='<?= e($student['email'] ?? '') ?>'>
						<i class="fas fa-edit text-orange m-0 mr-3 fs-5"></i>
						<span class="text-black">Edit profile</span>
					</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</div>
<section id='coursejoi' class="pt-1">
	<div class="container">
		<div class="row g-4 justify-content-center">
			<div class="text-center mx-auto">
				<h4 class="text-orange">My Courses</h4>
				<p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
			</div>
			<?php foreach ($courses as $course): ?>
			<!-- Card item START -->
			<div class="col-lg-10 col-xl-7">
				<div class="card shadow p-2">
					<div class="row g-0">
						<div class="col-md-4 d-flex align-items-center">
							<img src="uploading/<?= e($course['image_courses']) ?>" class="rounded-3" alt="...">
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<div class="d-sm-flex justify-content-sm-between mb-2 mb-sm-3">
									<div>
										<h5 class="card-title mb-0"><?= e($course['title']) ?></h5>
										<p class="small mb-2 mb-sm-0">Professor at Sigma College</p>
									</div>
									<span class="h6 fw-light">4.3<i class="fas fa-star text-warning ms-1"></i></span>
								</div>
								<p class="mb-3"><?= e($course['description']) ?></p>
								<div class="d-sm-flex justify-content-sm-between align-items-center">
									<h6 class="text-orange mb-0">Digital Marketing</h6>
									<form action="/blog_learning" method='post'>
										<input type="text" value='<?= e($student['email'] ?? '') ?>' name='email' hidden>
										<input type="text" value='<?= e($course['course_id']) ?>' name='course_id' hidden>
										<button type="submit" class="btn btn-primary">Open course</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Card item END -->
			<?php endforeach ?>
		</div>
	</div>
</section>

<!-- Edit Personal Modal -->
<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body border border-orange p-4 m-4" style="background-color: #f8f9fa;">
				<h5 class="mb-4 text-orange">Edit Profile</h5>
				<form action="/get_edit" method="post" enctype="multipart/form-data">
					<div class="mb-3">
						<label for="image" class="form-label">Profile Image:</label>
						<input type="file" class="form-control" id="image" name="image" style="border-color: #ced4da;">
					</div>
					<div class="mb-3">
						<label for="name" class="form-label">Name:</label>
						<input type="text" class="form-control" id="name" name="name" style="border-color: #ced4da;" value='<?= e($student['name'] ?? '') ?>'>
					</div>
					<div class="mb-3">
						<label for="phone" class="form-label">Phone:</label>
						<input type="text" class="form-control" id="phone" name="phone" style="border-color: #ced4da;" value='<?= e($student['phone'] ?? '') ?>'>
					</div>
					<div class="mb-3">
						<label for="email" class="form-label">Email:</label>
						<input type="text" class="form-control" id="email" name="email" style="border-color: #ced4da;" value='<?= e($student['email'] ?? '') ?>'>
					</div>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-orange">Update</button>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
	$('.show-edit').click(function() {
		$('#name').val($(this).data('name'));
		$('#phone').val($(this).data('phone'));
		$('#email').val($(this).data('email'));
	});
});
</script>

<?= View::partial('layouts/public/footer') ?>
