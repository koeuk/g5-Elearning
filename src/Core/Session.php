<?php

namespace App\Core;

/**
 * Small convenience wrapper around $_SESSION.
 */
final class Session
{
    /** Keep a signed-in user for this long (seconds) — ~7 days. */
    private const LIFETIME = 60 * 60 * 24 * 7;

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        // Persist the login cookie across browser restarts so returning users
        // stay signed in, and keep the server-side session alive to match.
        ini_set('session.gc_maxlifetime', (string) self::LIFETIME);
        if (PHP_SAPI !== 'cli') {
            session_set_cookie_params([
                'lifetime' => self::LIFETIME,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        session_start();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /** Flash a one-time value (read once, then cleared). */
    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}
