<?php

namespace App\Models;

use App\Core\Database;

/**
 * `lessons` table (from lesson.mode / manage.model — the two were duplicates).
 */
final class Lesson
{
    /** @return array<int, array> */
    public static function forCourse(int $courseId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM lessons WHERE course_id = :id');
        $stmt->execute([':id' => $courseId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM lessons WHERE lesson_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function findByTitle(string $title): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM lessons WHERE title = :title');
        $stmt->execute([':title' => $title]);
        return $stmt->fetch();
    }

    /** Whether a lesson with this title exists in a course. Returns [] when absent. */
    public static function existsInCourse(int $courseId, string $title): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM lessons WHERE title = :title AND course_id = :id');
        $stmt->execute([':title' => $title, ':id' => $courseId]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    public static function create(string $title, string $description, string $video, int $courseId, bool $isFree = false): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO lessons (title, description, course_id, video, is_free)
             VALUES (:title, :description, :course_id, :video, :is_free)'
        );
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':course_id'   => $courseId,
            ':video'       => $video,
            ':is_free'     => $isFree ? 1 : 0,
        ]);
    }

    public static function update(int $id, string $title, string $description, string $video, bool $isFree = false): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE lessons SET title = :title, description = :description, video = :video, is_free = :is_free WHERE lesson_id = :id'
        );
        $stmt->execute([
            ':id'          => $id,
            ':title'       => $title,
            ':description' => $description,
            ':video'       => $video,
            ':is_free'     => $isFree ? 1 : 0,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM lessons WHERE lesson_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
