<?php

namespace App\Middleware;

use App\Middleware\Middleware;

class RoleMiddleware implements Middleware
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle($request, $next)
    {
        $userRole = $this->getUserRole();

        if (!$this->hasRole($userRole, $this->roles)) {
            $this->unauthorized();
        }

        return $next($request);
    }

    private function getUserRole(): ?int
    {
        return $_SESSION['user']['role_id'] ?? null;
    }

    private function hasRole(?int $userRoleId, array $requiredRoles): bool
    {
        if (!$userRoleId) {
            return false;
        }

        $roleMap = [
            'admin' => 1,
            'recruiter' => 2,
            'recruteur' => 2,  
            'candidate' => 3,
            'candidat' => 3    
        ];

        foreach ($requiredRoles as $roleName) {
            if (isset($roleMap[$roleName]) && $roleMap[$roleName] === $userRoleId) {
                return true;
            }
        }

        return false;
    }

    private function unauthorized(): void
    {
        header('Location: /403');
        exit;
    }
}