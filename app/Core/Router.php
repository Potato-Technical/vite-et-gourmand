<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path)) {
            $path = '/';
        }

        $path = $this->normalizePath($path);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            $this->notFound();

            return;
        }

        if (is_array($handler) && is_string($handler[0])) {
            $controller = new $handler[0]();
            $action = $handler[1];

            $controller->$action();

            return;
        }

        call_user_func($handler);
    }

    private function normalizePath(string $path): string
    {
        if ($path === '/') {
            return '/';
        }

        return '/' . trim($path, '/');
    }

    private function notFound(): void
    {
        http_response_code(404);

        require dirname(__DIR__) . '/Views/errors/404.php';
    }
}