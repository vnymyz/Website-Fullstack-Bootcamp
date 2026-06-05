<?php
abstract class Controller {
    protected function view(string $path, array $data = []): void {
        extract($data);
        $file = __DIR__ . '/../app/views/' . $path . '.php';
        if (!file_exists($file)) {
            http_response_code(500);
            die("View not found: $path");
        }
        require $file;
    }

    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function requireAuth(): void {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(string $key, mixed $default = ''): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = ''): mixed {
        return $_GET[$key] ?? $default;
    }
}
