<?php

/*
 * Global helper functions, autoloaded via composer "files".
 * Kept intentionally small — they replace app/core/url.php and app/core/debug.php.
 */

if (!function_exists('urlIs')) {
    /** True when the current request path equals the given value. */
    function urlIs(string $value): bool
    {
        return ($_SERVER['REQUEST_URI'] ?? '') === $value;
    }
}

if (!function_exists('e')) {
    /** HTML-escape a value for safe output in views. */
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    /** Send a Location header and stop execution. */
    function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('old')) {
    /** Re-populate a form field from the last POST submission. */
    function old(string $key, string $default = ''): string
    {
        return e($_POST[$key] ?? $default);
    }
}

if (!function_exists('dd')) {
    /** Dump and die — replacement for the old debug() helper. */
    function dd(mixed ...$values): never
    {
        echo '<pre>';
        foreach ($values as $value) {
            var_dump($value);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('uploadedImage')) {
    /**
     * Resolve an image stored under public/uploading to a usable src, falling
     * back to a bundled placeholder when the file is missing or the name empty.
     * Prevents the broken-image 404s the admin lists produced for users/courses
     * whose stored file no longer exists on disk.
     */
    function uploadedImage(string $file, string $fallback = 'assets/images/avatar/01.jpg'): string
    {
        $file = trim($file);
        if ($file !== '' && is_file(dirname(__DIR__, 2) . '/public/uploading/' . $file)) {
            return 'uploading/' . $file;
        }
        return $fallback;
    }
}
