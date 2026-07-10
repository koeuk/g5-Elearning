<?php

namespace App\Core;

/**
 * Central place for password hashing/verification and the logged-in user.
 *
 * This replaces the broken pattern found throughout the old models, e.g.:
 *
 *     $hashed = password_hash($stored, PASSWORD_DEFAULT);   // hash the stored value
 *     if (password_verify($input, $hashed)) { ... }         // then "verify" against it
 *
 * That only ever amounted to a convoluted plaintext comparison. Here we hash
 * once on write and verify against the stored hash on read. Legacy rows that
 * still hold a plaintext password are accepted once, then transparently
 * upgraded to a proper hash.
 */
final class Auth
{
    /** Hash a plaintext password for storage. */
    public static function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    /** True when the stored value already looks like a password_hash() output. */
    public static function isHashed(string $stored): bool
    {
        return password_get_info($stored)['algo'] !== null;
    }

    /**
     * Verify a plaintext password against the stored value.
     *
     * Accepts both proper hashes and legacy plaintext (transitional), so
     * existing seed data keeps working until the row is re-hashed.
     */
    public static function verify(string $plain, string $stored): bool
    {
        if (self::isHashed($stored)) {
            return password_verify($plain, $stored);
        }

        // Legacy plaintext row — constant-time compare.
        return hash_equals($stored, $plain);
    }

    /** True when a stored hash should be re-computed (legacy or outdated cost). */
    public static function needsRehash(string $stored): bool
    {
        if (!self::isHashed($stored)) {
            return true;
        }

        return password_needs_rehash($stored, PASSWORD_DEFAULT);
    }

    // ---- logged-in user (session) -------------------------------------------

    /** Persist the authenticated user in the session. */
    public static function login(array $user): void
    {
        Session::set('user', [
            'user_id'  => $user['user_id'] ?? null,
            'name'     => $user['name'] ?? '',
            'email'    => $user['email'] ?? '',
            'roles_id' => $user['roles_id'] ?? null,
        ]);
    }

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        $user = self::user();
        return isset($user['user_id']) ? (int) $user['user_id'] : null;
    }

    public static function role(): ?int
    {
        $user = self::user();
        return isset($user['roles_id']) ? (int) $user['roles_id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        Session::forget('user');
    }
}
