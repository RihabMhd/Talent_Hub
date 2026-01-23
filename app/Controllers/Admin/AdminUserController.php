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

    // afficher liste dial users li mazal ma verifiench
    public function pending()
    {
        $pendingUsers = $this->verificationService->getPendingUsers();
        
        // kanpassiw data l template b3da nrenderiwh
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
        
        // n7aydou messages mn session ba3d ma naffichewhom bach may b9awch ytaffichou
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    // verify user - kay acceptih
    public function verify(int $id)
    {
        $result = $this->verificationService->verifyUser($id);
        
        // n checkew result w nsauviw message f session
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        // nrja3 l pending page
        header('Location: /admin/users/pending');
        exit;
    }

    // reject user - kayrefusih
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