<?php

namespace App\Core;

/**
 * Tiny .env loader.
 *
 * The app reads configuration through getenv() (see config/database.php), but
 * PHP does not read a .env file on its own. This loads KEY=VALUE lines from the
 * project root .env into the environment so credentials are available without
 * exporting them into the shell first.
 *
 * Intentionally minimal: no quoting rules, no interpolation. Real environment
 * variables always win — a value already set in the environment is never
 * overwritten, so production config (real env vars) takes precedence over .env.
 */
final class Env
{
    public static function load(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            // Skip comments and any line without a key=value pair.
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            if ($key === '' || getenv($key) !== false) {
                continue;
            }

            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}
