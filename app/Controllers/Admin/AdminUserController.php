<?php
namespace App\Controllers\Admin;

use App\Services\UserVerificationService;

class AdminUserController
{
   
    private UserVerificationService $verificationService;
    private $twig;

    public function __construct(UserVerificationService $verificationService, $twig = null)
    {
        $this->verificationService = $verificationService;
        $this->twig = $twig;
    }

    /**
     * Display list of users pending verification
     */
    public function pending()
    {
        $pendingUsers = $this->verificationService->getPendingUsers();
        
        echo $this->twig->render('admin/users/pending.html.twig', [
            'users' => $pendingUsers,
            'current_user' => $_SESSION['user'] ?? null,
            'session' => $_SESSION ?? [],
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
        
        // Clear messages after displaying
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    /**
     * Verify a user
     */
    public function verify(int $id)
    {
        $result = $this->verificationService->verifyUser($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: /admin/users/pending');
        exit;
    }

    /**
     * Reject a user
     */
    public function reject(int $id)
    {
        $result = $this->verificationService->rejectUser($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header('Location: /admin/users/pending');
        exit;
    }
}