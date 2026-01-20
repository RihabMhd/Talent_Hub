<?php
namespace App\Services;

use App\Repository\UserRepository;

class AuthService
{
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function login(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        
        $roleNames = [1 => 'admin', 2 => 'recruteur', 3 => 'candidat'];
        
        $fullName = trim($user['nom'] . ' ' . $user['prenom']);
        
        $_SESSION['user'] = [
            'id' => $user['id'],  
            'name' => $fullName,
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role' => $roleNames[$user['role_id']] ?? 'candidat'
        ];
        
        return $user;
    }
    
    public function register(array $userData): ?int
    {
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        return $this->userRepository->create($userData);
    }
    
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
    
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
    }
    
    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
    
    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->userRepository->updatePassword($userId, $hashedPassword);
    }
}