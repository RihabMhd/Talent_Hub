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

    // redirect l dashboard 7sb role dial user
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

    // afficher form dial login
    public function showLoginForm(): void
    {
        // ila deja connecté nrja3oh l dashboard
        if ($this->authService->isLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        Twig::render('auth/login.twig', [
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success')
        ]);
    }

    // traitement dial login
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // validation - khass email w password
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            $this->redirect('/login');
            return;
        }

        // n checkew credentials f database
        $user = $this->authService->login($email, $password);

        if ($user) {
            $_SESSION['success'] = 'Login successful';
            $this->redirectToDashboard($user['role_id']);
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            $this->redirect('/login');
        }
    }

    // afficher form dial inscription
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

    // traitement dial inscription
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
        
        // n checkew ila passwords matchاw
        if ($userData['password'] !== $passwordConfirm) {
            $_SESSION['error'] = 'Passwords do not match';
            $_SESSION['old'] = $userData;
            $this->redirect('/register');
            return;
        }

        // validation basic dial fields
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

        // n créew user f database
        $userId = $this->authService->register($userData);

        if ($userId) {
            $_SESSION['success'] = 'Registration successful, please login';
            $this->redirect('/login');
        } else {
            // imken email deja exist
            $_SESSION['error'] = 'Registration failed, email may already exist';
            $_SESSION['old'] = $userData;
            $this->redirect('/register');
        }
    }

    // logout - n7aydou session
    public function logout(): void
    {
        $this->authService->logout();
        session_start(); 
        $_SESSION['success'] = 'Logged out successfully';
        $this->redirect('/login');
    }

    // afficher form dial tbdil password
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

    // traitement dial tbdil password
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

        // n checkew ila password jdid strong bzaf
        $validation = $this->validator->validatePasswordStrength($newPassword);
        if ($validation['strength'] === 'weak') {
            $_SESSION['error'] = 'Password is too weak: ' . implode(', ', $validation['feedback']);
            $this->redirect('/change-password');
            return;
        }

        $user = $this->authService->getCurrentUser();

        // nbdlo password - kanverifiw old password awalan
        $success = $this->authService->changePassword($user['id'], $oldPassword, $newPassword);

        if ($success) {
            $_SESSION['success'] = 'Password changed successfully';
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Current password is incorrect';
            $this->redirect('/change-password');
        }
    }

    // redirect 7sb role - kol role 3ndo dashboard dyalo
    private function redirectToDashboard(int $roleId): void
    {
        switch ($roleId) {
            case 1:  // admin
                $this->redirect('/admin/dashboard');
                break;
            case 2:  // recruiter
                $this->redirect('/recruiter/dashboard');
                break;
            case 3:  // candidate
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

    // njibou flash message w n7aydoh mn session
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