<?php

namespace App\Controllers\Student;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

/**
 * Student ordering / checkout screen ("/orders").
 *
 * GET  → show the student's pending cart and the payment modals.
 * POST → record a payment for each selected course, then re-render with the
 *        success banner. Replaces the inline processing that used to live at the
 *        top of students/order.view.php.
 */
final class OrderController extends Controller
{
    public function index(): void
    {
        $student = $this->currentStudent();
        $studentId = isset($student['user_id']) ? (int) $student['user_id'] : 0;

        $paid = false;
        if ($this->isPost() && $studentId > 0) {
            $paid = $this->processPayment($studentId);
        }

        $this->view('students/order', [
            'email'  => $student['email'] ?? '',
            'orders' => $studentId > 0 ? Order::pendingForWithCourse($studentId) : [],
            'paid'   => $paid,
        ]);
    }

    /**
     * Resolve the acting student. Prefers the session user once student auth is
     * migrated; falls back to the posted email the legacy pages still send.
     *
     * @return array<string, mixed>
     */
    private function currentStudent(): array
    {
        if (Auth::role() === User::ROLE_STUDENT && ($user = Auth::user()) !== null) {
            return $user;
        }

        $email = $this->input('email');
        return $email !== '' ? User::findByEmail($email, User::ROLE_STUDENT) : [];
    }

    /**
     * Record a payment for each selected, not-yet-paid course.
     *
     * The payment is dated with the purchase date (today). The card's expiry
     * field is a form-only detail and is deliberately NOT written to the
     * payments.date column — doing so stored a wrong/future date and crashed
     * when the value was not a full YYYY-MM-DD date.
     *
     * @return bool true only when at least one payment was actually recorded
     */
    private function processPayment(int $studentId): bool
    {
        $selection = $this->input('selectioned');
        $total     = $this->input('totals');

        if ($selection === '' || $total === '') {
            return false;
        }

        $today    = date('Y-m-d');
        $recorded = 0;

        foreach (array_map('intval', explode(',', $selection)) as $courseId) {
            if ($courseId <= 0 || Payment::exists($studentId, $courseId) !== []) {
                continue;
            }
            $course = Course::find($courseId);
            if ($course !== false) {
                Payment::add($courseId, $studentId, $today, (string) $course['price']);
                $recorded++;
            }
        }

        return $recorded > 0;
    }
}
