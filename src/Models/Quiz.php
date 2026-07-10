<?php

namespace App\Models;

use App\Core\Database;

/**
 * `quizzes` table (from lesson.mode / manage.model).
 */
final class Quiz
{
    /** @return array<int, array> */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quizzes');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** @return array<int, array> */
    public static function forLesson(int $lessonId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM quizzes WHERE lesson_id = :id');
        $stmt->execute([':id' => $lessonId]);
        return $stmt->fetchAll();
    }

    public static function create(int $lessonId, string $content): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO quizzes (lesson_id, content) VALUES (:id, :content)');
        $stmt->execute([':id' => $lessonId, ':content' => $content]);
    }

    public static function update(int $quizId, int $lessonId, string $content): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE quizzes SET lesson_id = :lesson_id, content = :content WHERE quiz_id = :quiz_id'
        );
        $stmt->execute([':lesson_id' => $lessonId, ':content' => $content, ':quiz_id' => $quizId]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM quizzes WHERE quiz_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
