<?php

namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

/**
 * The `users` table backs admins (roles_id=1), trainers (roles_id=2) and
 * students (roles_id=3). This class consolidates what used to be spread across
 * user.model, student.model, trainer.model, trainer.info.model and admin.access.
 */
final class User
{
    public const ROLE_ADMIN   = 1;
    public const ROLE_TRAINER = 2;
    public const ROLE_STUDENT = 3;

    /** Find a user by id. */
    public static function find(int $id): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Find a user by email, optionally constrained to a role. Returns [] when absent. */
    public static function findByEmail(string $email, ?int $role = null): array
    {
        $sql    = 'SELECT * FROM users WHERE email = :email';
        $params = [':email' => $email];
        if ($role !== null) {
            $sql .= ' AND roles_id = :role';
            $params[':role'] = $role;
        }
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    /** Find an admin by (display) name. Returns [] when absent. */
    public static function findAdminByName(string $name): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE name = :name AND roles_id = 1');
        $stmt->execute([':name' => $name]);
        return $stmt->rowCount() > 0 ? $stmt->fetch() : [];
    }

    /** The (single) admin account. */
    public static function admin(): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE roles_id = 1');
        $stmt->execute();
        return $stmt->fetch();
    }

    /** Resolve a trainer's id from their name. */
    public static function trainerIdByName(string $name): array|false
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE name = :name AND roles_id = 2');
        $stmt->execute([':name' => $name]);
        return $stmt->fetch();
    }

    /** @return array<int, array> */
    public static function students(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE roles_id = 3');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** @return array<int, array> */
    public static function trainers(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE roles_id = 2');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** All non-admin users (trainers + students). */
    public static function nonAdmins(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE roles_id != 1');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a user with a properly hashed password.
     * $role defaults to student.
     */
    public static function create(
        string $name,
        string $email,
        string $password,
        string $phone,
        string $gender,
        string $image,
        int $role = self::ROLE_STUDENT
    ): bool {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (name, email, password, gender, roles_id, phone, profile_image, created_at)
             VALUES (:name, :email, :password, :gender, :role, :phone, :image, NOW())'
        );
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => Auth::hash($password),
            ':gender'   => $gender,
            ':role'     => $role,
            ':phone'    => $phone,
            ':image'    => $image,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Update a user. When $password is non-empty it is re-hashed; otherwise the
     * existing password is left untouched.
     */
    public static function update(
        int $id,
        string $name,
        string $email,
        string $phone,
        string $gender,
        string $image,
        string $password = ''
    ): bool {
        if ($password !== '') {
            $stmt = Database::connection()->prepare(
                'UPDATE users SET name = :name, email = :email, password = :password,
                 gender = :gender, phone = :phone, profile_image = :image WHERE user_id = :id'
            );
            $stmt->execute([
                ':name'     => $name,
                ':email'    => $email,
                ':password' => Auth::hash($password),
                ':gender'   => $gender,
                ':phone'    => $phone,
                ':image'    => $image,
                ':id'       => $id,
            ]);
        } else {
            $stmt = Database::connection()->prepare(
                'UPDATE users SET name = :name, email = :email, gender = :gender,
                 phone = :phone, profile_image = :image WHERE user_id = :id'
            );
            $stmt->execute([
                ':name'   => $name,
                ':email'  => $email,
                ':gender' => $gender,
                ':phone'  => $phone,
                ':image'  => $image,
                ':id'     => $id,
            ]);
        }
        return $stmt->rowCount() > 0;
    }

    /** Change just the password (already validated), storing a fresh hash. */
    public static function setPassword(int $id, string $newPassword): bool
    {
        $stmt = Database::connection()->prepare('UPDATE users SET password = :password WHERE user_id = :id');
        $stmt->execute([':password' => Auth::hash($newPassword), ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
    }
}
