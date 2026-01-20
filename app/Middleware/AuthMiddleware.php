<?php
namespace App\Middleware;
use App\Middleware\Middleware;

class AuthMiddleware implements Middleware
{

    public function handle($request, $next) {
        if(!$this->isAuthentication()){
            $this->unauthenticated();
        }
        return $next($request);
    }


    private function isAuthentication(): bool
    {
        return isset($_SESSION['user']);
    }

    private function unauthenticated()
{
    header('Location: /login');  
    exit;
}
}
