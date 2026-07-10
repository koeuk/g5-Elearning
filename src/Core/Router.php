<?php

namespace App\Core;

/**
 * Minimal path-based router.
 *
 * Routes map a URI path + HTTP method to a [Controller::class, 'method'] pair.
 * This replaces the three route tables ($publicPages / $adminPages /
 * $standalonePages) in the old public/index.php — the layout is now chosen
 * by each controller action, not by which table a route lived in.
 */
final class Router
{
    /** @var array<string, array<string, callable|array{0:class-string,1:string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array|callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /** Register a handler for both GET and POST (form screens that self-submit). */
    public function any(string $path, array|callable $handler): void
    {
        $this->get($path, $handler);
        $this->post($path, $handler);
    }

    public function dispatch(string $uri, string $method): void
    {
        $path   = parse_url($uri, PHP_URL_PATH) ?: '/';
        $method = strtoupper($method);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            $this->notFound();
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->{$action}();
            return;
        }

        $handler();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo View::render('errors/404');
    }
}
