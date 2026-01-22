<?php
namespace App\Controllers\Admin;

use App\Services\UserService;

class UserController
{
    private UserService $userService;
    private $twig;

    public function __construct(UserService $userService, $twig = null)
    {
        $this->userService = $userService;
        $this->twig = $twig;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        
        echo $this->twig->render('admin/users/pending.html.twig', [
            'users' => $users,
            'current_user' => $_SESSION['user'] ?? null,
            'session' => $_SESSION ?? [],
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
        
  
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }
}