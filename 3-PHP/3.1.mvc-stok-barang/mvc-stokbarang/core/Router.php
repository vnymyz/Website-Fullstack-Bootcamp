<?php
class Router {
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void {
        $this->routes[] = ['GET', $path, $controller, $method];
    }

    public function post(string $path, string $controller, string $method): void {
        $this->routes[] = ['POST', $path, $controller, $method];
    }

    public function dispatch(string $uri, string $httpMethod): void {
        $uri = strtok($uri, '?');
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as [$method, $path, $controller, $action]) {
            if ($method === $httpMethod && $path === $uri) {
                require_once __DIR__ . '/../app/controllers/' . $controller . '.php';
                $obj = new $controller();
                $obj->$action();
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 — Halaman tidak ditemukan</h1>';
    }
}
