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
    
    /**
     * Get all users pending verification
     */
    public function getPendingUsers(): array
    {
        $users = $this->userRepository->findPendingVerification();
        
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }
    
    /**
     * Get all verified users
     */
    public function getVerifiedUsers(): array
    {
        $users = $this->userRepository->findVerified();
        
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }
    
    /**
     * Verify a user by ID
     */
    public function verifyUser(int $userId): array
    {
        // Check if user exists
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Check if already verified
        if ($user['is_verified']) {
            return [
                'success' => false,
                'message' => 'User is already verified'
            ];
        }
        
        // Verify the user
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
    
    /**
     * Reject a user by ID
     */
    public function rejectUser(int $userId): array
    {
        // Check if user exists
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Check if already verified
        if ($user['is_verified']) {
            return [
                'success' => false,
                'message' => 'Cannot reject a verified user'
            ];
        }
        
        // Reject (delete) the user
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
    
    /**
     * Get pending users count
     */
    public function getPendingCount(): int
    {
        return count($this->userRepository->findPendingVerification());
    }
}