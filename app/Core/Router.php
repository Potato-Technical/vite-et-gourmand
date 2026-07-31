<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /**
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path)) {
            $path = '/';
        }

        $path = $this->normalizePath($path);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler !== null) {
            $this->executeHandler($handler);

            return;
        }

        if ($this->pathExists($path)) {
            $this->methodNotAllowed($path);

            return;
        }

        $this->notFound();
    }

    private function addRoute(
        string $method,
        string $path,
        callable|array $handler
    ): void {
        $this->routes[$method][$this->normalizePath($path)] = $handler;
    }

    private function executeHandler(callable|array $handler): void
    {
        if (
            is_array($handler)
            && isset($handler[0], $handler[1])
            && is_string($handler[0])
            && is_string($handler[1])
        ) {
            $controller = new $handler[0]();
            $action = $handler[1];

            $controller->$action();

            return;
        }

        call_user_func($handler);
    }

    private function pathExists(string $path): bool
    {
        foreach ($this->routes as $routesByMethod) {
            if (array_key_exists($path, $routesByMethod)) {
                return true;
            }
        }

        return false;
    }

    private function allowedMethodsForPath(string $path): array
    {
        $allowedMethods = [];

        foreach ($this->routes as $method => $routesByMethod) {
            if (array_key_exists($path, $routesByMethod)) {
                $allowedMethods[] = $method;
            }
        }

        return $allowedMethods;
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

    private function methodNotAllowed(string $path): void
    {
        $allowedMethods = $this->allowedMethodsForPath($path);

        if ($allowedMethods !== []) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }

        http_response_code(405);

        require dirname(__DIR__) . '/Views/errors/405.php';
    }
}