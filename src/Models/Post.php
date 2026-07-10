<?php

namespace App\Models;

use App\Core\Database;

/**
 * `posts` table (from employee.model). Retained as-is for parity; not wired to
 * any active route in the original app.
 */
final class Post
{
    public static function create(string $title, string $description): bool
    {
        $stmt = Database::connection()->prepare('INSERT INTO posts (title, description) VALUES (:title, :description)');
        $stmt->execute([':title' => $title, ':description' => $description]);
        return $stmt->rowCount() > 0;
    }

    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** @return array<int, array> */
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM posts');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function update(int $id, string $title, string $description): bool
    {
        $stmt = Database::connection()->prepare('UPDATE posts SET title = :title, description = :description WHERE id = :id');
        $stmt->execute([':title' => $title, ':description' => $description, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
