# Migration to the standard PSR-4 structure

This document tracks the move from the original framework-less, `require`-based
layout (`app/`) to a standard, Composer-autoloaded, class-based structure
(`src/` + `resources/views/`). It is the map to finish the migration.

## New layout

```
g5-Elearning/
├── composer.json            ← PSR-4: App\ => src/, plus src/Core/helpers.php
├── public/
│   └── index.php            ← bootstrap: autoload → session → Router::dispatch
├── routes/
│   └── web.php              ← route table (path → [Controller, action])
├── src/
│   ├── Core/                ← framework plumbing
│   │   ├── Database.php     ← shared PDO (replaces `global $connection`)
│   │   ├── Router.php       ← replaces the 3 route arrays
│   │   ├── Controller.php   ← base controller (view/redirect/input helpers)
│   │   ├── View.php         ← renders resources/views + wraps in a layout
│   │   ├── Session.php
│   │   ├── Auth.php         ← password hashing/verify + logged-in user
│   │   ├── Validation.php   ← form + credential validation
│   │   └── helpers.php      ← e(), old(), redirect(), urlIs(), dd()
│   ├── Controllers/         ← App\Controllers\* (being migrated)
│   └── Models/              ← App\Models\* (done)
├── resources/
│   └── views/               ← page templates + layouts/{public,admin}
├── config/database.php
├── database/learning.sql
└── tests/                   ← PHPUnit (AuthTest guards the password fix)
```

The legacy `app/` directory is kept temporarily as a reference. Delete it once
every route below is migrated.

## What is done

- **Composer + autoloading** (`composer.json`, `composer dump-autoload`).
- **Core** layer (Database, Router, Controller, View, Session, Auth, Validation, helpers).
- **All models** converted to `App\Models\*` classes. The old files had
  colliding function names across files (e.g. `accountExist`, `payments`,
  `getStudent`, the identical `lesson.mode.php`/`manage.model.php`) — namespaced
  classes resolve those cleanly.
- **Auth fix.** The old sign-in did `password_verify($input, password_hash($stored))`
  — a convoluted plaintext compare. Now: `Auth::hash()` on write,
  `Auth::verify()` on read. Legacy plaintext rows still authenticate and are
  flagged by `Auth::needsRehash()` for transparent upgrade. Covered by `tests/AuthTest.php`.
- **Home page** (`/`) migrated end-to-end as the reference vertical slice.
- **Student course-grid home** (`/student`) migrated: identity now comes from
  `Auth::user()` (not a posted email), the controller gathers all data
  (categories+dropdown, course cards with enrolment/purchase flags, popular
  courses, cart count) and the add-to-cart POST is handled server-side. The
  `/student` route accepts GET and POST. The `students/payments/payment` modal
  partial no longer reads `$_POST`.

## Model function → class-method map

Use this when migrating a view/controller that still calls an old global function.

| Old global function | New call |
|---|---|
| `getCategories()` | `Category::all()` |
| `getCategory($id)` / `getCategoryName($id)` | `Category::find($id)` |
| `getIdCategory($title)` | `Category::findByTitle($title)` |
| `createCategory($t,$d,$img)` | `Category::create($t,$d,$img)` |
| `updateCategory($t,$d,$img,$id)` | `Category::update($id,$t,$d,$img)` — **arg order changed** |
| `deleteCategory($id)` | `Category::delete($id)` |
| `getNumberOfCourseInCategory($id)` | `Course::countInCategory($id)` |
| `getCoursesOnCategory($id)` | `Course::byCategory($id)` |
| `getCourses()` / `getCouses()` / `getCourseed()` | `Course::all()` |
| `getCourse($id)` / `getThecourseJoin($id)` / `getCousesSold($id)` | `Course::find($id)` |
| `getCourseRespone($id)` | `Course::byTrainer($id)` |
| `getCourse($email)` (trainer join) / `courseExis($email)` | `Course::byTrainerEmail($email)` |
| `createCourse($t,$d,$cat,$date,$img,$uid,$price)` | `Course::create($t,$d,$cat,$date,$img,$uid,$price)` |
| `updateCourse($id,$t,$d,$uid,$cat,$price,$img)` | `Course::update(...)` (same order) |
| `updateCourseNoImg($id,$t,$d,$uid,$cat,$price)` | `Course::updateWithoutImage(...)` |
| `deleteCourse($id)` | `Course::delete($id)` (also fixes the wrong-table bug) |
| `searchCourse($t)` | `Course::search($t)` |
| `getTeacher($id)` / `getTrainerName($id)` / `getStudent($id)` / `getUsers($id)` | `User::find($id)` |
| `accountExist($email)` | `User::findByEmail($email)` or `User::findByEmail($email, User::ROLE_STUDENT)` |
| `account($email)` / `getTrainersInfo($email)` | `User::findByEmail($email, User::ROLE_TRAINER)` |
| `accountAdminExist($name)` | `User::findAdminByName($name)` |
| `getAdmin()` | `User::admin()` |
| `getIdTrainer($name)` | `User::trainerIdByName($name)` |
| `getStudents()` / `students()` (admin list) | `User::students()` |
| `getTrainer()` | `User::trainers()` |
| `students()` (non-admin) | `User::nonAdmins()` |
| `createStudent(...)` / `addStudent(...)` | `User::create(..., User::ROLE_STUDENT)` |
| `addTrainer(...)` | `User::create(..., User::ROLE_TRAINER)` |
| `updateTrainer(...)` | `User::update(...)` |
| `deleteStudent($id)` / `deleteTrainer($id)` | `User::delete($id)` |
| `getTheLessons($id)` | `Lesson::forCourse($id)` |
| `getLessonById($id)` | `Lesson::find($id)` |
| `getLessonByTitle($t)` / `getTheLessonsbyname($t)` | `Lesson::findByTitle($t)` |
| `lessonExist($id,$t)` | `Lesson::existsInCourse($id,$t)` |
| `addLesson($t,$d,$v,$cid)` | `Lesson::create($t,$d,$v,$cid)` |
| `editLesson($id,$t,$d,$v)` | `Lesson::update($id,$t,$d,$v)` |
| `deleteLesson($id)` | `Lesson::delete($id)` |
| `getQuizzes()` | `Quiz::all()` |
| `getQuizzesbylessonId($id)` | `Quiz::forLesson($id)` |
| `addQuizzes($lid,$c)` | `Quiz::create($lid,$c)` |
| `editQuiz($qid,$lid,$c)` | `Quiz::update($qid,$lid,$c)` |
| `deleteQuiz($id)` | `Quiz::delete($id)` |
| `getQuizzesSumitbylessonId($id)` | `QuizSubmission::forLesson($id)` |
| `addsumit($uid,$lid,$img)` | `QuizSubmission::create($uid,$lid,$img)` |
| `quizResultSumitExist($img)` | `QuizSubmission::existsByImage($img)` |
| `deleteQuizsumit($id)` | `QuizSubmission::delete($id)` |
| `orderExist($uid,$cid)` | `Order::exists($uid,$cid)` |
| `getTheorder($uid)` | `Order::pendingFor($uid)` |
| `getAddOrderExist($uid,$cid)` | `Order::pending($uid,$cid)` |
| `addLesson($cid,$uid)` (payment.model) | `Order::add($cid,$uid)` |
| `getPaid($cid)` | `Order::markPaid($cid)` |
| `deleteOrders($cid)` | `Order::deleteForCourse($cid)` |
| `getTheJoinercourse($cid)` | `Order::joinCount($cid)` |
| `addPayments($cid,$uid,$date,$total)` | `Payment::add($cid,$uid,$date,$total)` |
| `getPaymentExist($uid,$cid)` | `Payment::exists($uid,$cid)` |
| `getCoursePaid($uid)` | `Payment::forUser($uid)` |
| `getPaymentMn($cid)` | `Payment::forCourse($cid)` |
| `getRevenues()` / `getRevenuese()` | `Payment::all()` |
| `applySignin($e,$p)` / `applySigninAdmin($n,$p)` | `Validation::signin(...)` / `Validation::adminSignin(...)` |
| `requireInformation(...)` | `Validation::registration(...)` |
| `requirePasswordChange(...)` / `requirePasswordChanges(...)` | `Validation::passwordChange($stored,$cur,$new,$confirm)` |
| `strongPassword($p)` | `Validation::strongPassword($p)` |

## Route → controller map (to build)

Register each in `routes/web.php` as the controller is created. Paths are kept
identical to the originals. Layout: `public` = `$this->public()`, `admin` =
`$this->admin()`, standalone = `$this->view(...)` with no layout.

| Path | Method | New controller::action | Layout |
|---|---|---|---|
| `/` | GET | `HomeController::index` ✅ | public |
| `/trainers` | GET | `TrainerController::index` | public |
| `/admin_home` | GET | `Admin\DashboardController::index` | admin |
| `/courses_as_admin`, `/viewCourse` | GET | `Admin\CourseController::index` | admin |
| `/courseEdit` | GET | `Admin\CourseController::edit` | admin |
| `/createCourse` | POST | `Admin\CourseController::store` | — |
| `/updateCourse` | POST | `Admin\CourseController::update` | — |
| `/deleteCourse` | POST | `Admin\CourseController::destroy` | — |
| `/categories` | GET | `Admin\CategoryController::index` | admin |
| `/insertCategory` | POST | `Admin\CategoryController::store` | — |
| `/editCategory` | POST | `Admin\CategoryController::update` | — |
| `/deleteCategory` | POST | `Admin\CategoryController::destroy` | — |
| `/list_trainer` | GET | `Admin\TrainerController::index` | admin |
| `/add_trainer` | POST | `Admin\TrainerController::store` | — |
| `/delete_trainer` | POST | `Admin\TrainerController::destroy` | — |
| `/list_student`, `/detail` | GET | `Admin\StudentController::index`/`show` | admin |
| `/addStudent` | POST | `Admin\StudentController::store` | — |
| `/deleteStudent` | POST | `Admin\StudentController::destroy` | — |
| `/admin_signin` `/admin_access` | GET/POST | `Admin\AuthController::showLogin`/`login` | — |
| `/admin_password` `/admin_password_comfirm` | GET/POST | `Admin\PasswordController` | — |
| `/admin` | GET | `Admin\AuthController::start` | — |
| `/signin` `/access` | GET/POST | `Student\AuthController::showLogin`/`login` | — |
| `/signup` `/create_student` | GET/POST | `Student\AuthController::showRegister`/`register` | — |
| `/student` `/student_profile` | GET | `Student\HomeController`/`ProfileController` | — |
| `/edit` `/get_edit` | GET/POST | `Student\ProfileController::edit`/`update` | — |
| `/student_password` `/student_password_comfirm` | GET/POST | `Student\PasswordController` | — |
| `/orders` | GET/POST | `Student\OrderController` | — |
| `/course` `/blog_learning` | GET | `CourseController::show`/`blog` | — |
| `/trainer` `/trainer_access` | GET/POST | `Trainer\AuthController` | — |
| `/trainer_home` `/trainer_manage` `/trainer_detail` | GET | `Trainer\*Controller` | — |
| `/trainer_edits` | POST | `Trainer\ProfileController::update` | — |
| `/trainer_password` `/trainer_passwrod_comfirm` | GET/POST | `Trainer\PasswordController` | — |

## Migrating a view (recipe)

1. In the `.view.php`, delete the top `require 'database/...'` and
   `require 'models/...'` lines (classes autoload).
2. Add `use App\Models\...;` for the models it uses.
3. Replace each old global call using the table above.
4. Move request handling (`$_POST` reads, redirects, form processing) out of the
   view into the controller action; pass results to the view via the `$data`
   array of `$this->view(...)`.
5. Replace the old ad-hoc login flow with `Auth::login($user)` /
   `Auth::user()` so pages identify the current user by session instead of a
   hidden `email` form field.
