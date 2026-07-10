<?php

/*
 * Application routes.
 *
 * Replaces the three route tables ($publicPages / $adminPages /
 * $standalonePages) from the old public/index.php. The layout each page uses
 * is now chosen inside the controller action, not by which table it lived in.
 *
 * Paths are kept identical to the originals so existing links keep working.
 */

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\CourseController as AdminCourseController;
use App\Controllers\Admin\StudentController as AdminStudentController;
use App\Controllers\Admin\TrainerController as AdminTrainerController;
use App\Controllers\Admin\PasswordController as AdminPasswordController;
use App\Controllers\Student\AuthController as StudentAuthController;
use App\Controllers\Student\HomeController as StudentHomeController;
use App\Controllers\Student\OrderController;
use App\Controllers\Trainer\AuthController as TrainerAuthController;
use App\Controllers\Trainer\HomeController as TrainerHomeController;
use App\Controllers\Trainer\ProfileController as TrainerProfileController;

$router = new Router();

/* -------------------------------------------------------------------------- */
/* Public site                                                                */
/* -------------------------------------------------------------------------- */
$router->get('/', [HomeController::class, 'index']);

/* -------------------------------------------------------------------------- */
/* Students                                                                   */
/* -------------------------------------------------------------------------- */
/* Authentication. */
$router->get('/signin', [StudentAuthController::class, 'showLogin']);      // sign-in form
$router->post('/access', [StudentAuthController::class, 'login']);         // credential check
$router->get('/signup', [StudentAuthController::class, 'showRegister']);   // registration form
$router->post('/create_student', [StudentAuthController::class, 'register']); // create account
$router->get('/logout', [StudentAuthController::class, 'logout']);         // clear session

/* Authenticated area. Handles GET and POST (the course grid self-submits to
   add a course to the cart). */
$router->any('/student', [StudentHomeController::class, 'index']);         // course-grid home

/* Ordering/checkout screen — self-submits, so it handles GET and POST. */
$router->any('/orders', [OrderController::class, 'index']);

/* -------------------------------------------------------------------------- */
/* Admin                                                                      */
/* -------------------------------------------------------------------------- */
$router->get('/admin', [AuthController::class, 'start']);          // welcome / logout landing
$router->get('/admin_signin', [AuthController::class, 'showLogin']); // sign-in form
$router->post('/admin_access', [AuthController::class, 'login']);    // credential check
$router->get('/admin_home', [DashboardController::class, 'index']);  // dashboard

/* Categories. */
$router->get('/categories', [AdminCategoryController::class, 'index']);
$router->post('/insertCategory', [AdminCategoryController::class, 'store']);
$router->post('/editCategory', [AdminCategoryController::class, 'update']);
$router->post('/deleteCategory', [AdminCategoryController::class, 'destroy']);

/* Courses. */
$router->get('/courses_as_admin', [AdminCourseController::class, 'index']);
$router->get('/viewCourse', [AdminCourseController::class, 'index']);
$router->post('/courseEdit', [AdminCourseController::class, 'edit']);
$router->post('/createCourse', [AdminCourseController::class, 'store']);
$router->post('/updateCourse', [AdminCourseController::class, 'update']);
$router->post('/deleteCourse', [AdminCourseController::class, 'destroy']);

/* Trainers. */
$router->get('/list_trainer', [AdminTrainerController::class, 'index']);
$router->post('/add_trainer', [AdminTrainerController::class, 'store']);
$router->post('/delete_trainer', [AdminTrainerController::class, 'destroy']);

/* Students. */
$router->get('/list_student', [AdminStudentController::class, 'index']);
$router->post('/addStudent', [AdminStudentController::class, 'store']);
$router->post('/deleteStudent', [AdminStudentController::class, 'destroy']);

/* Change password. */
$router->get('/admin_password', [AdminPasswordController::class, 'edit']);
$router->post('/admin_password_comfirm', [AdminPasswordController::class, 'update']);

/* -------------------------------------------------------------------------- */
/* Trainers                                                                   */
/* -------------------------------------------------------------------------- */
$router->get('/trainer_signin', [TrainerAuthController::class, 'showLogin']); // sign-in form
$router->post('/trainer_access', [TrainerAuthController::class, 'login']);    // credential check
$router->get('/trainer_logout', [TrainerAuthController::class, 'logout']);    // clear session
$router->get('/trainer', [TrainerHomeController::class, 'index']);            // dashboard
$router->post('/trainer_edits', [TrainerProfileController::class, 'update']);              // edit profile
$router->post('/trainer_password_comfirm', [TrainerProfileController::class, 'updatePassword']); // change password

/*
 * The remaining areas (admin, students, trainers, courses) are migrated
 * controller-by-controller. As each App\Controllers class is added, register
 * its routes here using the same paths as the legacy tables. See MIGRATION.md
 * for the full path → controller map.
 */

return $router;
