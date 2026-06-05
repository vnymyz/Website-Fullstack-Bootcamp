<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login(): void {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/stock');
        }
        $this->view('auth/login', ['error' => $_SESSION['flash_error'] ?? null]);
        unset($_SESSION['flash_error']);
    }

    public function authenticate(): void {
        $email    = trim($this->post('email'));
        $password = $this->post('password');

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Email dan password wajib diisi.';
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Email atau password salah.';
            $this->redirect('/login');
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        $this->redirect('/stock');
    }

    public function logout(): void {
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
