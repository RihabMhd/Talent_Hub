<?php
namespace App\Middleware;

interface Middleware
{
    public function handle($request, $next);
}
