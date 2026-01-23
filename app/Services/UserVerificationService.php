<?php
namespace App\Services;

use App\Repository\UserRepository;

class UserVerificationService
{
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    

    public function getPendingUsers(): array
    {
        $users = $this->userRepository->findPendingVerification();
        
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }
    
   
    public function getVerifiedUsers(): array
    {
        $users = $this->userRepository->findVerified();
        
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }
    
   
    public function verifyUser(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        if ($user['is_verified']) {
            return [
                'success' => false,
                'message' => 'User is already verified'
            ];
        }
        
        $result = $this->userRepository->verifyUser($userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'User verified successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to verify user'
        ];
    }
    
  
    public function rejectUser(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        if ($user['is_verified']) {
            return [
                'success' => false,
                'message' => 'Cannot reject a verified user'
            ];
        }
        
        $result = $this->userRepository->rejectUser($userId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'User rejected and removed successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to reject user'
        ];
    }
    
   
    public function getPendingCount(): int
    {
        return count($this->userRepository->findPendingVerification());
    }
}