<?php
namespace App\Services;
use App\Repository\UserRepository;
class UserService
{
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function getAllUsers(): array
    {
        $users = $this->userRepository->findAll();
        
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }
   
    public function getUserById(int $id): ?array
    {
        $user = $this->userRepository->findById($id);
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    public function getUserByEmail(string $email): ?array
    {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    public function getUserCount(): int
    {
        return count($this->userRepository->findAll());
    }
}