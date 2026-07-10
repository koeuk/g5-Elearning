<?php

namespace App\Core;

use RuntimeException;

/**
 * Renders a PHP template from resources/views, optionally wrapped in a layout.
 *
 * Layouts mirror the three original route groups:
 *   'public' → public site header/navbar/footer
 *   'admin'  → admin dashboard header/navbar/footer
 *   null     → standalone (auth screens, self-contained pages)
 */
final class View
{
    private const VIEW_PATH = __DIR__ . '/../../resources/views/';

    /**
     * Render a view and return its HTML as a string.
     *
     * @param string               $view   e.g. 'students/signin' (resolves to students/signin.view.php)
     * @param array<string, mixed> $data   variables made available to the template
     * @param 'public'|'admin'|null $layout layout to wrap the view in
     */
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $content = self::capture(self::VIEW_PATH . $view . '.view.php', $data);

        if ($layout === null) {
            return $content;
        }

        return self::capture(self::VIEW_PATH . "layouts/{$layout}.php", $data + ['content' => $content]);
    }

    /** Render a view and echo it to the output buffer. */
    public static function display(string $view, array $data = [], ?string $layout = null): void
    {
        echo self::render($view, $data, $layout);
    }

    /**
     * Include a partial (layout fragment, e.g. 'layouts/public/navbar') and
     * return its output. Used from within layout files.
     */
    public static function partial(string $partial, array $data = []): string
    {
        return self::capture(self::VIEW_PATH . $partial . '.php', $data);
    }

    /** Execute a PHP template file in an isolated scope and capture its output. */
    private static function capture(string $file, array $data): string
    {
        if (!is_file($file)) {
            throw new RuntimeException("View not found: {$file}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
