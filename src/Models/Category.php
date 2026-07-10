<?php

namespace App\Models;

use App\Core\Database;

/**
 * `categories` table (from category.model).
 */
final class Category
{
    public static function create(string $title, string $description, string $image): bool
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO categories (title, description, image) VALUES (:title, :description, :image)'
        );
        $stmt->execute([':title' => $title, ':description' => $description, ':image' => $image]);
        return $stmt->rowCount() > 0;
    }

    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE category_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** @return array<int, array> */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * All categories with their course count in a single query, avoiding a
     * per-category Course::countInCategory() call in the view.
     *
     * @return array<int, array> each row includes a `course_count` column
     */
    public static function allWithCourseCount(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT cat.*,
                    (SELECT COUNT(*) FROM courses c WHERE c.category_id = cat.category_id) AS course_count
             FROM categories cat'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Resolve a category id from its title. */
    public static function findByTitle(string $title): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE title = :title');
        $stmt->execute([':title' => $title]);
        return $stmt->fetch();
    }

    public static function update(int $id, string $title, string $description, string $image): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE categories SET title = :title, description = :description, image = :image
             WHERE category_id = :id'
        );
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':image'       => $image,
            ':id'          => $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM categories WHERE category_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
