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

    /** Record an auto-graded multiple-choice result for a lesson. */
    public static function record(int $userId, int $lessonId, int $score, int $total, string $date): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO quiz_sumit (user_id, lesson_id, score, total, created_at)
             VALUES (:user_id, :lesson_id, :score, :total, :date)'
        );
        $stmt->execute([
            ':user_id'   => $userId,
            ':lesson_id' => $lessonId,
            ':score'     => $score,
            ':total'     => $total,
            ':date'      => $date,
        ]);
    }

    /** The most recent graded result for a user + lesson, or [] if none. */
    public static function latestResult(int $userId, int $lessonId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM quiz_sumit WHERE user_id = :u AND lesson_id = :l AND score IS NOT NULL
             ORDER BY sumit_id DESC LIMIT 1'
        );
        $stmt->execute([':u' => $userId, ':l' => $lessonId]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
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
