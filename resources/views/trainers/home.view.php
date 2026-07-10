<?php
/**
 * Trainer dashboard. Data comes from Trainer\HomeController.
 *
 * @var array $trainer                          the logged-in trainer's user row
 * @var array<int, array> $myCourses            courses owned by this trainer
 * @var array<int, array> $allCourses           every course
 * @var array<int, string> $trainerNames        user_id => trainer name (for All courses)
 */
$trainerAvatar = trim((string) ($trainer['profile_image'] ?? '')) !== ''
    ? 'uploading/' . $trainer['profile_image']
    : 'assets/images/avatar/01.jpg';
$title = strtolower((string) ($trainer['gender'] ?? '')) === 'male' ? 'Mr.' : 'Ms.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>E-Learning — Trainer</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" type="text/css" href="vendor/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="vendor/css/style.css">
</head>
<body>
<?= \App\Core\View::partial('layouts/partials/flash') ?>

<!-- **************** MAIN CONTENT START **************** -->
<main>
<section style='height: 200px;background-image: url("assets/images/bg/pastel-2571378_1280.jpg");'>
    <div class="row mb-4">
        <div class="col-lg-8 text-center mx-auto">
            <h2 class="fs-1">Welcome <?= e($title) ?> <span class='text-orange'><?= e($trainer['name'] ?? '') ?></span> To E-learning</h2>
            <p class="mb-0">Information Technology Courses to expand your skills and boost your career &amp; salary</p>
        </div>
        <div class='d-flex justify-content-end p-5'>
            <a href="/trainer_logout"><button class="btn btn-danger mb-0"><i class="fas fa-sign-in-alt me-2"></i>Log Out</button></a>
        </div>
    </div>
</section>

<section class="pt-4" style="background-color: rgba(0, 0, 0,0.05);">
    <div class="container">
        <div class="mt-4">
            <button type="button" id='personal' class="btn btn-outline-orange"><i class="bi bi-info-circle"></i> My Personal Information</button>
            <button type="button" id='respone' class="btn btn-outline-orange"><i class="bi bi-person-check-fill"></i> My Courses</button>
            <button type="button" id='courses' class="btn btn-outline-orange"><i class="bi bi-briefcase-fill"></i> All the Courses</button>
        </div>
    </div>
</section>

<!-- Personal information -->
<div class="d-flex justify-content-center" style="background-color: rgba(0, 0, 0,0.05);">
    <section id="personals">
        <div class="container">
            <div class="row shadow align-items-center border rounded p-4" style="max-width:600px;margin-top: 24px;">
                <div class="text-center">
                    <img class="img-fluid rounded-circle mb-3" src="<?= e($trainerAvatar) ?>" alt="" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="m-1"><?= e($trainer['name'] ?? '') ?></h4>
                </div>
                <div class="col-lg-7 py-4 px-3">
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <span>Phone:</span><h6 class="m-0"><?= e($trainer['phone'] ?? '') ?></h6>
                    </div>
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <span>Email:</span><h6 class="m-0"><?= e($trainer['email'] ?? '') ?></h6>
                    </div>
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <span>Courses:</span><h6 class="m-0"><?= count($myCourses) ?> Courses</h6>
                    </div>
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <span>Experiences:</span><h6 class="m-0">1 year</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- My courses -->
<section id='responsible' class="pt-1" style="background-color: rgba(0, 0, 0,0.05); display:none;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="text-center mx-auto">
                <h4 class="text-orange">My Courses</h4>
                <p class="mb-0">Courses you teach</p>
            </div>
            <?php if (empty($myCourses)) : ?>
                <p class="text-center">You have no courses yet.</p>
            <?php else : ?>
                <?php foreach ($myCourses as $course) : ?>
                <div class="col-lg-10 col-xl-7">
                    <div class="card shadow p-2">
                        <div class="row g-0">
                            <div class="col-md-4 d-flex align-items-center">
                                <img src="uploading/<?= e($course['image_courses'] ?? '') ?>" class="rounded-3" alt="course">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title mb-0"><?= e($course['title'] ?? '') ?></h5>
                                    <p class="text-truncate-2 mb-3"><?= e($course['description'] ?? '') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- All courses -->
<section id='allcourses' class="pt-1" style="background-color: rgba(0, 0, 0,0.05); display:none;">
    <div class="container">
        <div class="text-center mx-auto">
            <h4 class="text-orange">All the Courses</h4>
            <p class="mb-5">Every course on the platform</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($allCourses as $course) : ?>
            <div class="col-lg-10 col-xl-6">
                <div class="card shadow p-2">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploading/<?= e($course['image_courses'] ?? '') ?>" class="rounded-3" alt="course">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title mb-0"><?= e($course['title'] ?? '') ?></h5>
                                <p class="small mb-2">trainer : <span class='text-info'><?= e($trainerNames[(int) $course['user_id']] ?? '') ?></span></p>
                                <p class="text-truncate-2 mb-3"><?= e($course['description'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
</main>
<!-- **************** MAIN CONTENT END **************** -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tab switching between the three panels.
    document.getElementById('personal').addEventListener('click', function () {
        document.getElementById('responsible').style.display = 'none';
        document.getElementById('allcourses').style.display = 'none';
        document.getElementById('personals').style.display = 'block';
    });
    document.getElementById('respone').addEventListener('click', function () {
        document.getElementById('responsible').style.display = 'block';
        document.getElementById('allcourses').style.display = 'none';
        document.getElementById('personals').style.display = 'none';
    });
    document.getElementById('courses').addEventListener('click', function () {
        document.getElementById('responsible').style.display = 'none';
        document.getElementById('allcourses').style.display = 'block';
        document.getElementById('personals').style.display = 'none';
    });
</script>
</body>
</html>
