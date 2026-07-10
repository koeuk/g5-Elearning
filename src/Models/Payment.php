<?php

namespace App\Models;

use App\Core\Database;

/**
 * `payments` table — a completed course purchase. From payment.model,
 * student.model and admin.manage.model.
 */
final class Payment
{
    /**
     * Record a payment and mark the related orders paid.
     */
    public static function add(int $courseId, int $userId, string $date, string $total): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO payments (user_id, course_id, total, date) VALUES (:user, :course, :total, :date)'
        );
        $stmt->execute([':course' => $courseId, ':user' => $userId, ':date' => $date, ':total' => $total]);

        Order::markPaid($courseId);
    }

    /** Whether a payment already exists for this user + course. Returns [] when absent. */
    public static function exists(int $userId, int $courseId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE user_id = :user AND course_id = :course');
        $stmt->execute([':user' => $userId, ':course' => $courseId]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    /** Payments made by a user (their purchased courses). */
    public static function forUser(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE user_id = :id');
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll();
    }

    /** Payments for a course. */
    public static function forCourse(int $courseId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE course_id = :id');
        $stmt->execute([':id' => $courseId]);
        return $stmt->fetchAll();
    }

    /** @return array<int, array> every payment (revenue report). */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
