<?php

/*
 * Single front controller for the whole application.
 *
 * The document root is this `public/` directory. Every request that is not a
 * real file (see .htaccess) lands here and is dispatched from the route tables
 * below. Application code lives one level up in ../app and is reached through
 * the include_path set here, so controllers/models/views keep using their
 * existing root-relative requires (e.g. `require 'models/x.model.php'`).
 */

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Resolve `require 'models/...'`, `require 'views/...'`, etc. against app/.
set_include_path(APP_PATH);
// Uploads are written with paths relative to the web root (e.g. 'uploading/x').
chdir(PUBLIC_PATH);

require 'core/url.php';
require 'database/database.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

/* Pages wrapped in the public site layout. */
$publicPages = [
    '/'         => 'controllers/home/home.controller.php',
    '/trainers' => 'controllers/trainers/trainer.controller.php',
];

/* Pages wrapped in the admin dashboard layout (they render, not redirect). */
$adminPages = [
    '/admin_home'       => 'controllers/admin/admin.controller.php',
    '/courses_as_admin' => 'controllers/admin/courses/course.controller.php',
    '/viewCourse'       => 'controllers/admin/courses/course.controller.php',
    '/courseEdit'       => 'controllers/admin/courses/course_edit.controller.php',
    '/categories'       => 'controllers/admin/category/category.controller.php',
    '/list_trainer'     => 'controllers/admin/trainer/trainer.controller.php',
    '/list_student'     => 'controllers/admin/students/student_controller.php',
];

/* Standalone pages: auth flows, form handlers that redirect, self-contained views. */
$standalonePages = [
    // Admin authentication & password
    '/admin_signin'            => 'controllers/admin/signin/login.controller.php',
    '/admin_access'            => 'controllers/admin/signin/access.controller.php',
    '/admin_password'          => 'controllers/admin/passwords/passwrod.controller.php',
    '/admin_password_comfirm'  => 'controllers/admin/passwords/getnew.controller.php',
    '/admin'                   => 'controllers/admin/start_admin.controller.php',

    // Admin form handlers (do their work, then redirect)
    '/createCourse'   => 'controllers/admin/courses/create_course.controller.php',
    '/updateCourse'   => 'controllers/admin/courses/update_course.controller.php',
    '/deleteCourse'   => 'controllers/admin/courses/course_delete.controller.php',
    '/insertCategory' => 'controllers/admin/category/insert_category.controller.php',
    '/editCategory'   => 'controllers/admin/category/category_edit.controller.php',
    '/deleteCategory' => 'controllers/admin/category/category_delete.controller.php',
    '/add_trainer'    => 'controllers/admin/trainer/add.controller.php',
    '/delete_trainer' => 'controllers/admin/trainer/delete.controller.php',
    '/addStudent'     => 'controllers/admin/students/student_addstudent.controller.php',
    '/deleteStudent'  => 'controllers/admin/students/student_delete.controller.php',

    // Student authentication, profile & password
    '/signin'                  => 'controllers/students/signin/signin.controller.php',
    '/access'                  => 'controllers/students/signin/access.controller.php',
    '/signup'                  => 'controllers/students/signup/signup.controller.php',
    '/create_student'          => 'controllers/students/signup/create.user.controller.php',
    '/student'                 => 'controllers/students/home.controller.php',
    '/student_profile'         => 'controllers/students/profile.controller.php',
    '/edit'                    => 'controllers/students/edit/edit.controller.php',
    '/get_edit'                => 'controllers/students/edit/get.edit.controller.php',
    '/student_password'        => 'controllers/students/passwords/passwrod.controller.php',
    '/student_password_comfirm'=> 'controllers/students/passwords/getnew.controller.php',
    '/formChangeNumber'        => 'controllers/students/forgetPassword/formChangePassword.controller.php',
    '/orders'                  => 'controllers/students/payments/order.controller.php',

    // Courses (student-facing)
    '/course'        => 'controllers/courses/course.controller.php',
    '/blog_learning' => 'controllers/courses/blog_learning.controller.php',

    // Trainer authentication, home & management
    '/trainerLogin'            => 'controllers/trainer/trainerLogin.controller.php',
    '/trainerAccess'           => 'controllers/trainer/trainerLoginProcess.controller.php',
    '/trainer'                 => 'controllers/trainers/signin/login.controller.php',
    '/trainer_access'          => 'controllers/trainers/signin/access.controller.php',
    '/trainer_home'            => 'controllers/trainers/hom.controller.php',
    '/trainer_manage'          => 'controllers/trainers/trainer.controller.php',
    '/trainer_detail'          => 'controllers/trainers/trainer.controller.php',
    '/trainer_edits'           => 'controllers/trainers/edit.controller.php',
    '/trainer_password'        => 'controllers/trainers/passwords/passwrod.controller.php',
    '/trainer_passwrod_comfirm'=> 'controllers/trainers/passwords/getnew.controller.php',

    // Admin-side student detail
    '/detail' => 'controllers/admin/students/student_detail.controller.php',
];

if (isset($publicPages[$uri])) {
    require 'layouts/header.php';
    require 'layouts/navbar.php';
    require $publicPages[$uri];
    require 'layouts/footer.php';
} elseif (isset($adminPages[$uri])) {
    require 'layouts/admin/header.php';
    require 'layouts/admin/navbar.php';
    require $adminPages[$uri];
    require 'layouts/admin/footer.php';
} elseif (isset($standalonePages[$uri])) {
    require $standalonePages[$uri];
} else {
    http_response_code(404);
    require 'views/errors/404.php';
}
