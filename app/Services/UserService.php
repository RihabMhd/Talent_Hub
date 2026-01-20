<?php
namespace App\Services;
use App\Services\ValidatorService;
use App\Repository\UserRepository;
class UserService
{
    private UserRepository $userRepository;
    private ValidatorService $validator;
    
    public function __construct(UserRepository $userRepository, ValidatorService $validator)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
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