<?php

namespace App\Models;

use App\Core\Database;

/**
 * `quiz_sumit` table — student quiz answer submissions
 * (from lesson.mode / manage.model). Table name kept as in the schema.
 */
final class QuizSubmission
{
    /** @return array<int, array> */
    public static function forLesson(int $lessonId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quiz_sumit WHERE lesson_id = :id');
        $stmt->execute([':id' => $lessonId]);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, int $lessonId, string $image): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO quiz_sumit (user_id, lesson_id, image) VALUES (:user_id, :lesson_id, :image)'
        );
        $stmt->execute([':user_id' => $userId, ':lesson_id' => $lessonId, ':image' => $image]);
    }

    /** Whether a submission with this image exists. Returns [] when absent. */
    public static function existsByImage(string $image): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quiz_sumit WHERE image = :image');
        $stmt->execute([':image' => $image]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM quiz_sumit WHERE sumit_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
