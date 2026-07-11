<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

/**
 * "My Courses" ("/my_courses") — the courses this student has actually bought.
 *
 * Same batched approach as the home grid: one payments query builds a
 * course_id set, then the full course list (already carrying trainer details)
 * is filtered down to the owned ones in memory — no per-course lookup.
 */
final class MyCoursesController extends Controller
{
    /** GET /my_courses — only purchased courses, each opening the classroom. */
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_STUDENT) {
            $this->redirect('/signin');
        }

        $student   = Auth::user() ?? [];
        $studentId = (int) ($student['user_id'] ?? 0);

        $paidIds = [];
        foreach (Payment::forUser($studentId) as $pay) {
            $paidIds[(int) $pay['course_id']] = true;
        }

        $owned = [];
        foreach (Course::allWithTrainer() as $course) {
            if (isset($paidIds[(int) $course['course_id']])) {
                $owned[] = $course;
            }
        }

        $this->view('students/my_courses', [
            'student'   => $student,
            'courses'   => $owned,
            'cartCount' => count(Order::pendingFor($studentId)),
        ]);
    }
}
