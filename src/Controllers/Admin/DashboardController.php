<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;

/**
 * Admin dashboard ("/admin_home").
 *
 * Replaces app/controllers/admin/admin.controller + the inline queries that
 * lived in views/admin/admin.view.php (admin.manage.model). All data is now
 * gathered here and handed to the view, which only renders.
 */
final class DashboardController extends Controller
{
    /** GET /admin_home — summary tiles, popular courses and recent payments. */
    public function index(): void
    {
        if (Auth::role() !== User::ROLE_ADMIN) {
            $this->redirect('/admin_signin');
        }

        $payments = Payment::all();

        $this->admin('admin/admin', [
            'stats'    => $this->stats($payments),
            'popular'  => $this->popularCourses($payments),
            'payments' => $this->recentPayments($payments),
        ]);
    }

    /** Top summary tiles. */
    private function stats(array $payments): array
    {
        $revenue = 0;
        foreach ($payments as $pay) {
            $revenue += (int) $pay['total'];
        }

        return [
            'categories' => count(Category::all()),
            'courses'    => count(Course::all()),
            'revenue'    => $revenue,
            'users'      => count(User::nonAdmins()),
        ];
    }

    /**
     * Courses ranked by number of payments, most popular first.
     *
     * @return array<int, array{title: string, count: int}>
     */
    private function popularCourses(array $payments): array
    {
        $counts = array_count_values(array_column($payments, 'course_id'));
        arsort($counts);

        $rows = [];
        foreach ($counts as $courseId => $count) {
            $course = Course::find((int) $courseId);
            $rows[] = [
                'title' => $course['title'] ?? '(deleted course)',
                'count' => $count,
            ];
        }
        return $rows;
    }

    /**
     * Payment rows resolved to course title and user name for display.
     *
     * @return array<int, array{title: string, user: string, date: string, total: string}>
     */
    private function recentPayments(array $payments): array
    {
        $rows = [];
        foreach ($payments as $pay) {
            $course = Course::find((int) $pay['course_id']);
            $user   = User::find((int) $pay['user_id']);
            $rows[] = [
                'title' => $course['title'] ?? '(deleted course)',
                'user'  => $user['name'] ?? '(deleted user)',
                'date'  => (string) $pay['date'],
                'total' => (string) $pay['total'],
            ];
        }
        return $rows;
    }
}
