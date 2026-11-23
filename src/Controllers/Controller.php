<?php

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $data['view'] = $view;
        extract($data);
        require __DIR__ . '/../Views/layout.php';
    }

    protected function redirect(string $path, string $message = ''): void
    {
        if ($message !== '') {
            $_SESSION['message'] = $message;
        }
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function requireEmployee(): void
    {
        $id = (int) ($_SESSION['user']['user_id'] ?? 0);
        if ($id === 0) {
            $this->redirect('/login');
        }
        $role = (new User())->role($id);
        if ($role !== 'employee' && $role !== 'admin') {
            unset($_SESSION['user']);
            $this->redirect('/login');
        }
        $_SESSION['user']['role'] = $role;
    }

    protected function requireAdmin(): void
    {
        $this->requireEmployee();
        if ($_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
    }
}
