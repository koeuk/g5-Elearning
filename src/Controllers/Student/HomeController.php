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

        $this->view('students/home', [
            'student'    => $student,
            'categories' => $this->categories(),
            'courses'    => $this->courses($studentId),
            'topCourses' => $this->topCourses(),
            'cartCount'  => count(Order::pendingFor($studentId)),
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
     * Categories enriched with their course count and the courses they contain
     * (for the navbar dropdown).
     *
     * @return array<int, array>
     */
    private function categories(): array
    {
        $rows = [];
        foreach (Category::all() as $category) {
            $id     = (int) $category['category_id'];
            $rows[] = $category + [
                'course_count' => Course::countInCategory($id),
                'courses'      => Course::byCategory($id),
            ];
        }
        return $rows;
    }

    /**
     * Every course with its trainer, enrolment count and this student's
     * purchase/cart state.
     *
     * @return array<int, array>
     */
    private function courses(int $studentId): array
    {
        $rows = [];
        foreach (Course::all() as $course) {
            $courseId = (int) $course['course_id'];
            $trainer  = User::find((int) $course['user_id']);
            $rows[]   = $course + [
                'trainer_name'  => $trainer['name'] ?? '',
                'trainer_image' => $trainer['profile_image'] ?? '',
                'enrolled'      => Order::joinCount($courseId),
                'paid'          => Payment::exists($studentId, $courseId) !== [],
                'in_cart'       => Order::pending($studentId, $courseId) !== [],
            ];
        }
        return $rows;
    }

    /**
     * Courses ranked by number of payments, most popular first.
     *
     * @return array<int, array{title: string, image_courses: string, count: int}>
     */
    private function topCourses(): array
    {
        $counts = array_count_values(array_column(Payment::all(), 'course_id'));
        arsort($counts);

        $rows = [];
        foreach ($counts as $courseId => $count) {
            $course = Course::find((int) $courseId);
            if ($course === false) {
                continue;
            }
            $rows[] = [
                'title'         => (string) $course['title'],
                'image_courses' => (string) $course['image_courses'],
                'count'         => $count,
            ];
        }
        return $rows;
    }
}
