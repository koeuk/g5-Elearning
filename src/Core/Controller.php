<?php

namespace App\Core;

/**
 * Base controller. Provides small helpers so action methods stay thin.
 */
abstract class Controller
{
    /** Render a view wrapped in a layout and echo it. */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        View::display($view, $data, $layout);
    }

    /** Render a public-site page. */
    protected function public(string $view, array $data = []): void
    {
        $this->view($view, $data, 'public');
    }

    /** Render an admin-dashboard page. */
    protected function admin(string $view, array $data = []): void
    {
        $this->view($view, $data, 'admin');
    }

    /** Redirect to another path and stop. */
    protected function redirect(string $path): never
    {
        redirect($path);
    }

    /** Read a POST field (trimmed) with a default. */
    protected function input(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    /** True when the current request is a POST. */
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
