<?php

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('login', ['title' => 'Connexion employé', 'error' => null]);
    }

    public function authenticate(): void
    {
        $login = (int) ($_POST['login'] ?? 0);
        $password = $_POST['password'] ?? '';
        $user = (new User(Database::getInstance()->getConnection()))->findByLogin($login);
        if (!$user || $user['role'] === 'citizen' || !password_verify($password, $user['password'])) {
            $this->view('login', ['title' => 'Connexion employé', 'error' => 'Identifiant ou mot de passe incorrect.']);
            return;
        }
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'role' => $user['role'],
        ];
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }
}
