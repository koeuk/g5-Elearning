<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

/**
 * Authenticated student landing page ("/student") — the course-grid home.
 *
 * Identity comes from the session (Auth::user()), not the posted email the
 * legacy page threaded through every form. The controller gathers everything
 * the view needs (categories, course cards with enrolment/purchase flags,
 * most-popular courses and the cart count) so the template only renders. A POST
 * carrying a course_id adds that course to the student's cart.
 *
 * All the page's data is loaded with a fixed handful of queries regardless of
 * how many courses or categories exist — the trainer/enrolment/purchase details
 * that used to be looked up per course (an N+1) now come from batched queries
 * and in-memory set lookups.
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_STUDENT) {
            $this->redirect('/signin');
        }

        $student   = Auth::user() ?? [];
        $studentId = (int) ($student['user_id'] ?? 0);

        if ($this->isPost() && $studentId > 0) {
            $this->addToCart($studentId);
        }

        // Shared lookups — one query each, no per-course/per-category fan-out.
        $allCourses = Course::allWithTrainer();
        $paidIds    = $this->indexByCourse(Payment::forUser($studentId));
        $cartOrders = Order::pendingFor($studentId);
        $cartIds    = $this->indexByCourse($cartOrders);

        $this->view('students/home', [
            'student'    => $student,
            'categories' => $this->categories($allCourses),
            'courses'    => $this->courses($allCourses, $paidIds, $cartIds),
            'topCourses' => $this->topCourses($allCourses),
            'cartCount'  => count($cartOrders),
        ]);
    }

    /** Add the posted course to the cart, unless it is already there. */
    private function addToCart(int $studentId): void
    {
        $courseId = (int) $this->input('course_id');
        if ($courseId > 0 && Order::exists($studentId, $courseId) === []) {
            Order::add($courseId, $studentId);
        }
    }

    /**
     * Categories enriched with their course count (from the query) and the
     * courses they contain, grouped in memory from the already-loaded list.
     *
     * @param array<int, array> $allCourses
     * @return array<int, array>
     */
    private function categories(array $allCourses): array
    {
        $byCategory = [];
        foreach ($allCourses as $course) {
            $byCategory[(int) $course['category_id']][] = $course;
        }

        $rows = [];
        foreach (Category::allWithCourseCount() as $category) {
            $id     = (int) $category['category_id'];
            $rows[] = $category + ['courses' => $byCategory[$id] ?? []];
        }
        return $rows;
    }

    /**
     * Every course (already carrying trainer_name/trainer_image/enrolled from
     * the batched query) plus this student's purchase/cart state, resolved by
     * set lookup rather than a query per course.
     *
     * @param array<int, array>  $allCourses
     * @param array<int, true>   $paidIds
     * @param array<int, true>   $cartIds
     * @return array<int, array>
     */
    private function courses(array $allCourses, array $paidIds, array $cartIds): array
    {
        $rows = [];
        foreach ($allCourses as $course) {
            $courseId = (int) $course['course_id'];
            $rows[]   = $course + [
                'paid'    => isset($paidIds[$courseId]),
                'in_cart' => isset($cartIds[$courseId]),
            ];
        }
        return $rows;
    }

    /**
     * Courses ranked by number of payments, most popular first. Titles/images
     * come from the already-loaded course list instead of a lookup per row.
     *
     * @param array<int, array> $allCourses
     * @return array<int, array{title: string, image_courses: string, count: int}>
     */
    private function topCourses(array $allCourses): array
    {
        $byId = [];
        foreach ($allCourses as $course) {
            $byId[(int) $course['course_id']] = $course;
        }

        $counts = array_count_values(array_map('intval', array_column(Payment::all(), 'course_id')));
        arsort($counts);

        $rows = [];
        foreach ($counts as $courseId => $count) {
            if (!isset($byId[$courseId])) {
                continue;
            }
            $rows[] = [
                'title'         => (string) $byId[$courseId]['title'],
                'image_courses' => (string) $byId[$courseId]['image_courses'],
                'count'         => $count,
            ];
        }
        return $rows;
    }

    /**
     * Build a course_id => true set from rows that each have a `course_id`,
     * for O(1) membership tests.
     *
     * @param array<int, array> $rows
     * @return array<int, true>
     */
    private function indexByCourse(array $rows): array
    {
        $ids = [];
        foreach ($rows as $row) {
            $ids[(int) $row['course_id']] = true;
        }
        return $ids;
    }
}
