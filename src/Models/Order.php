<?php

namespace App\Models;

use App\Core\Database;

/**
 * `orders` table — a course added to a student's cart (action='No') that
 * becomes paid (action='Yes'). From payment.model.
 */
final class Order
{
    /** Whether the given user already has an order for the course. Returns [] when absent. */
    public static function exists(int $userId, int $courseId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM orders WHERE course_id = :course AND user_id = :user');
        $stmt->execute([':course' => $courseId, ':user' => $userId]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    /** Unpaid orders (cart) for a user. */
    public static function pendingFor(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM orders WHERE user_id = :id AND action = :action');
        $stmt->execute([':id' => $userId, ':action' => 'No']);
        return $stmt->fetchAll();
    }

    /** A specific pending (unpaid) order. Returns [] when absent. */
    public static function pending(int $userId, int $courseId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM orders WHERE user_id = :id AND course_id = :course AND action = :action'
        );
        $stmt->execute([':id' => $userId, ':course' => $courseId, ':action' => 'No']);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    public static function add(int $courseId, int $userId): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO orders (course_id, user_id, action) VALUES (:course, :user, :action)'
        );
        $stmt->execute([':course' => $courseId, ':user' => $userId, ':action' => 'No']);
    }

    /** Mark all orders for a course as paid. */
    public static function markPaid(int $courseId): void
    {
        $stmt = Database::connection()->prepare("UPDATE orders SET action = 'Yes' WHERE course_id = :course");
        $stmt->execute([':course' => $courseId]);
    }

    public static function deleteForCourse(int $courseId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM orders WHERE course_id = :course');
        $stmt->execute([':course' => $courseId]);
    }

    /** How many students have joined (paid for) a course. */
    public static function joinCount(int $courseId): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM orders WHERE course_id = :course AND action = :action'
        );
        $stmt->execute([':course' => $courseId, ':action' => 'Yes']);
        return (int) $stmt->fetchColumn();
    }
}
