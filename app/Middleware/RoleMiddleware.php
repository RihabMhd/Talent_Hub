<?php

namespace App\Middleware;

use App\Middleware\Middleware;

class RoleMiddleware implements Middleware
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles; 
        // Les rôles li had middleware ghadi ycheckihom, par ex: ['admin', 'recruiter']
    }

    public function handle($request, $next)
    {
        $userRole = $this->getUserRole(); 
        // Katjib role_id dyal user mn session

        if (!$this->hasRole($userRole, $this->roles)) {
            $this->unauthorized(); 
            // Ila role dyal user ma kaynch f roles li khasshom → redirect l /403 w exit
        }

        return $next($request); 
        // Pipeline dyal middleware, katmchi l step jaya f traitement dyal request
    }

    private function getUserRole(): ?int
    {
        return $_SESSION['user']['role_id'] ?? null; 
        // Katchecka wach session user kayna w katsift role_id sinon null
    }

    private function hasRole(?int $userRoleId, array $requiredRoles): bool
    {
        if (!$userRoleId) {
            return false; 
            // Ila ma kaynach role_id → user ma authorizedch
        }

        $roleMap = [
            'admin' => 1,
            'recruiter' => 2,
            'recruteur' => 2,  
            'candidate' => 3,
            'candidat' => 3    
        ];
        // Mapping dyal names 

        foreach ($requiredRoles as $roleName) {
            if (isset($roleMap[$roleName]) && $roleMap[$roleName] === $userRoleId) {
                return true; 
                // Ila role dyal user matched m3a role li required → authorized
            }
        }

        return false; 
        // Ila ma matched walo → user ma authorizedch
    }

    private function unauthorized(): void
    {
        header('Location: /403');
        exit; 
        // Redirect l page dyal forbidden, exit khassek bach code ma ikmlch
    }
}
