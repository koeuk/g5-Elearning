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
use App\Controllers\Student\AuthController as StudentAuthController;
use App\Controllers\Student\HomeController as StudentHomeController;
use App\Controllers\Student\OrderController;

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

/*
 * The remaining areas (admin, students, trainers, courses) are migrated
 * controller-by-controller. As each App\Controllers class is added, register
 * its routes here using the same paths as the legacy tables. See MIGRATION.md
 * for the full path → controller map.
 */

return $router;
