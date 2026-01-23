<?php
namespace App\Middleware;

use App\Middleware\Middleware;

class AuthMiddleware implements Middleware
{

    public function handle($request, $next) {
        if(!$this->isAuthentication()){
            $this->unauthenticated(); 
            // Ila session dyal user ma kaynach → redirection l login w exit
            // Concept important: middleware katblock request ila ma kaynach auth
        }
        return $next($request); 
        // $next($request) → Hadi pipeline dyal middleware, katmchi l step jaya f traitement dyal request
    }

    private function isAuthentication(): bool
    {
        return isset($_SESSION['user']);
        // Katchecka wach session dyal user kayna, true ila kayna sinon false
    }

    private function unauthenticated()
    {
        header('Location: /login');  
        exit;
        // header() katdir redirect, exit khassek bach code ma ikmlch w ma ydirch processing
    }
}
