<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ValidatorService;
use App\Config\Twig;

class AuthController
{
    private AuthService $authService;
    private ValidatorService $validator;

    public function __construct(AuthService $authService, ValidatorService $validator)
    {
        $this->authService = $authService;
        $this->validator = $validator;
    }

    public function dashboard(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        $user = $this->authService->getCurrentUser();

        if (!$user || !isset($user['role_id'])) {
            $this->authService->logout();
            $this->redirect('/login');
            return;
        }

        $this->redirectToDashboard($user['role_id']);
    }

    public function showLoginForm(): void
    {
        if ($this->authService->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        Twig::render('auth/login.twig', [
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success')
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            $this->redirect('/login');
            return;
        }

        $user = $this->authService->login($email, $password);

        if ($user) {
            $_SESSION['success'] = 'Login successful';
            $this->redirectToDashboard($user['role_id']);
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            $this->redirect('/login');
        }
    }

    public function showRegisterForm(): void
    {
        if ($this->authService->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        Twig::render('auth/register.twig', [
            'error' => $this->getFlash('error'),
            'errors' => $this->getFlash('errors'),
            'old' => $this->getFlash('old') ?? []
        ]);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
            return;
        }

        $userData = [
            'nom' => $_POST['nom'] ?? '',
            'prenom' => $_POST['prenom'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role_id' => $_POST['role_id'] ?? 3
        ];

        $passwordConfirm = $_POST['password_confirm'] ?? '';
        if ($userData['password'] !== $passwordConfirm) {
            $_SESSION['error'] = 'Passwords do not match';
            $_SESSION['old'] = $userData;
            $this->redirect('/register');
            return;
        }

        // Basic validation
        $errors = [];
        if (empty($userData['nom'])) $errors['nom'] = 'Last name is required';
        if (empty($userData['prenom'])) $errors['prenom'] = 'First name is required';
        if (empty($userData['email'])) $errors['email'] = 'Email is required';
        if (empty($userData['password'])) $errors['password'] = 'Password is required';
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $userData;
            $this->redirect('/register');
            return;
        }

        $userId = $this->authService->register($userData);

        if ($userId) {
            $_SESSION['success'] = 'Registration successful, please login';
            $this->redirect('/login');
        } else {
            $_SESSION['error'] = 'Registration failed, email may already exist';
            $_SESSION['old'] = $userData;
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        session_start(); // Restart session for flash message
        $_SESSION['success'] = 'Logged out successfully';
        $this->redirect('/login');
    }

    public function showChangePasswordForm(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        Twig::render('auth/change-password.twig', [
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success'),
            'user' => $this->authService->getCurrentUser()
        ]);
    }

    public function changePassword(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/change-password');
            return;
        }

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('/change-password');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match';
            $this->redirect('/change-password');
            return;
        }

        $validation = $this->validator->validatePasswordStrength($newPassword);
        if ($validation['strength'] === 'weak') {
            $_SESSION['error'] = 'Password is too weak: ' . implode(', ', $validation['feedback']);
            $this->redirect('/change-password');
            return;
        }

        $user = $this->authService->getCurrentUser();

        $success = $this->authService->changePassword($user['id'], $oldPassword, $newPassword);

        if ($success) {
            $_SESSION['success'] = 'Password changed successfully';
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Current password is incorrect';
            $this->redirect('/change-password');
        }
    }

    private function redirectToDashboard(int $roleId): void
    {
        switch ($roleId) {
            case 1:
                $this->redirect('/admin/dashboard');
                break;
            case 2:
                $this->redirect('/recruiter/dashboard');
                break;
            case 3:
                $this->redirect('/candidate/dashboard');
                break;
            default:
                $this->redirect('/login');
                break;
        }
    }

    private function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }

    private function getFlash(string $key): mixed
    {
        if (isset($_SESSION[$key])) {
            $message = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $message;
        }
        return null;
    }
}