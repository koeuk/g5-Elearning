<?php

namespace App\Models;

use App\Core\Database;

/**
 * `courses` table. Consolidates admin.model, admin.manage.model (course parts),
 * trainer.info.model and the course lookups scattered across payment.model.
 */
final class Course
{
    public static function create(
        string $title,
        string $description,
        int $categoryId,
        string $date,
        string $image,
        int $userId,
        string $price
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO courses (title, description, user_id, category_id, date, price, image_courses)
             VALUES (:title, :description, :user_id, :category, :date, :price, :image)'
        );
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':user_id'     => $userId,
            ':category'    => $categoryId,
            ':date'        => $date,
            ':price'       => $price,
            ':image'       => $image,
        ]);
    }

    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM courses WHERE course_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** @return array<int, array> */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM courses');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * All courses enriched with their trainer's name/image and paid-enrollment
     * count, in one query. Replaces the per-course User::find() (called twice)
     * and Order::joinCount() lookups the home view used to do in a loop.
     *
     * @return array<int, array> rows include `trainer_name`, `trainer_image`, `enrolled`
     */
    public static function allWithTrainer(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT c.*,
                    u.name          AS trainer_name,
                    u.profile_image AS trainer_image,
                    (SELECT COUNT(*) FROM orders o
                     WHERE o.course_id = c.course_id AND o.action = :paid) AS enrolled
             FROM courses c
             LEFT JOIN users u ON c.user_id = u.user_id'
        );
        $stmt->execute([':paid' => 'Yes']);
        return $stmt->fetchAll();
    }

    /** Courses owned by a given trainer (user_id). */
    public static function byTrainer(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM courses WHERE user_id = :id');
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll();
    }

    /** Courses that belong to a trainer identified by email (INNER JOIN users). */
    public static function byTrainerEmail(string $email): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT c.* FROM courses c INNER JOIN users u ON c.user_id = u.user_id WHERE u.email = :email'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    /** @return array<int, array> */
    public static function byCategory(int $categoryId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM courses WHERE category_id = :id');
        $stmt->execute([':id' => $categoryId]);
        return $stmt->fetchAll();
    }

    public static function countInCategory(int $categoryId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM courses WHERE category_id = :id');
        $stmt->execute([':id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public static function update(
        int $id,
        string $title,
        string $description,
        int $userId,
        int $categoryId,
        string $price,
        string $image
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE courses SET title = :title, description = :description, user_id = :user_id,
             category_id = :category_id, price = :price, image_courses = :image WHERE course_id = :id'
        );
        $stmt->execute([
            ':id'          => $id,
            ':title'       => $title,
            ':description' => $description,
            ':user_id'     => $userId,
            ':category_id' => $categoryId,
            ':price'       => $price,
            ':image'       => $image,
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function updateWithoutImage(
        int $id,
        string $title,
        string $description,
        int $userId,
        int $categoryId,
        string $price
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE courses SET title = :title, description = :description, user_id = :user_id,
             category_id = :category_id, price = :price WHERE course_id = :id'
        );
        $stmt->execute([
            ':id'          => $id,
            ':title'       => $title,
            ':description' => $description,
            ':user_id'     => $userId,
            ':category_id' => $categoryId,
            ':price'       => $price,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete a course. (Fixes the original which referenced a non-existent
     * `course` table with a mismatched bind parameter.)
     */
    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM courses WHERE course_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /** Partial-title search. */
    public static function search(string $title): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM courses WHERE title LIKE :title');
        $stmt->execute([':title' => '%' . $title . '%']);
        return $stmt->fetchAll();
    }
}
