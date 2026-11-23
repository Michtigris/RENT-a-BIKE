<?php

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $data['view'] = $view;
        extract($data);
        require __DIR__ . '/../Views/layout.php';
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function requireEmployee(): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] === 'citizen') {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
    }
}
