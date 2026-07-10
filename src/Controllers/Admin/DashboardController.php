<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
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
        $this->requireAdmin();

        $payments = Payment::all();
        $popular  = $this->popularCourses($payments);

        $this->admin('admin/admin', [
            'stats'        => $this->stats($payments),
            'popular'      => $popular,
            'payments'     => $this->recentPayments($payments),
            // Chart data: courses by purchases, and students by courses bought.
            'coursesBought' => $popular,
            'topStudents'   => $this->studentPurchases($payments),
            // Growth tracking: the sign-up date of every user account. The view
            // buckets these by week/month/year/all on the client.
            'newUserDates' => $this->signupDates(),
        ]);
    }

    /**
     * Every user's account-creation date as 'Y-m-d' strings (all roles), for the
     * "new users over time" chart.
     *
     * @return array<int, string>
     */
    private function signupDates(): array
    {
        $rows = Database::connection()
            ->query('SELECT created_at FROM users WHERE created_at IS NOT NULL ORDER BY created_at')
            ->fetchAll();

        return array_map(static fn ($r) => substr((string) $r['created_at'], 0, 10), $rows);
    }

    /**
     * Students ranked by how many courses they have bought, most first.
     *
     * @return array<int, array{name: string, count: int}>
     */
    private function studentPurchases(array $payments): array
    {
        $counts = [];
        foreach ($payments as $pay) {
            $uid = (int) $pay['user_id'];
            $counts[$uid] = ($counts[$uid] ?? 0) + 1;
        }
        arsort($counts);

        $rows = [];
        foreach ($counts as $uid => $count) {
            $user   = User::find($uid);
            $rows[] = [
                'name'  => $user['name'] ?? '(deleted user)',
                'count' => $count,
            ];
        }
        return $rows;
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
