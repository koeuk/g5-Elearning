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
 * Student course listing for a single category ("/course").
 *
 * Reached from the category cards / navbar dropdown on the student home, which
 * POST a category `id`. Replaces app/controllers/courses/course.controller. A
 * POST carrying a course_id adds that course to the cart (same as the home
 * grid); the page then lists the chosen category's courses with this student's
 * purchase/cart state.
 */
final class CourseController extends Controller
{
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_STUDENT) {
            $this->redirect('/signin');
        }

        $student   = Auth::user() ?? [];
        $studentId = (int) ($student['user_id'] ?? 0);

        if ($this->isPost() && $studentId > 0) {
            $courseId = (int) $this->input('course_id');
            if ($courseId > 0 && Order::exists($studentId, $courseId) === []) {
                Order::add($courseId, $studentId);
            }
        }

        $categoryId = (int) $this->input('id');

        $this->view('courses/course', [
            'student'    => $student,
            'category'   => Category::find($categoryId) ?: [],
            'categoryId' => $categoryId,
            'courses'    => $this->courses($categoryId, $studentId),
        ]);
    }

    /**
     * Courses in the category, each with this student's purchase/cart state.
     *
     * @return array<int, array>
     */
    private function courses(int $categoryId, int $studentId): array
    {
        $rows = [];
        foreach (Course::byCategory($categoryId) as $course) {
            $courseId = (int) $course['course_id'];
            $rows[]   = $course + [
                'paid'    => Payment::exists($studentId, $courseId) !== [],
                'in_cart' => Order::pending($studentId, $courseId) !== [],
            ];
        }
        return $rows;
    }
}
