<?php

use App\Config\Router;
use App\Config\Twig;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

return function(Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $adminMiddleware = $middlewares['admin'];
    
    // admin routes group
    $router->group([
        'prefix' => '/admin',
        'middlewares' => [$authMiddleware, $adminMiddleware]
    ], function($router) use ($controllers) {
        
        // dashboard
        $router->get('/dashboard', function() {
            Twig::render('admin/dashboard.twig');
        });
        
        // user Management (future implementation)
        $router->get('/users', function() {
            echo "Admin - User List";
        });
        
        $router->get('/users/create', function() {
            echo "Admin - Create User";
        });
        
        $router->post('/users', function() {
            echo "Admin - Store User";
        });
        
        $router->get('/users/{id}/edit', function() {
            echo "Admin - Edit User";
        });
        
        $router->post('/users/{id}', function() {
            echo "Admin - Update User";
        });
        
        $router->post('/users/{id}/delete', function() {
            echo "Admin - Delete User";
        });
        
        // categories
        $router->get('/categories', function() {
            echo "Admin - Category List";
        });
        
        // tags
        $router->get('/tags', function() {
            echo "Admin - Tag List";
        });
        
        // job Offers
        $router->get('/jobs', function() {
            echo "Admin - Job List";
        });
        
        // applications
        $router->get('/applications', function() {
            echo "Admin - Application List";
        });
        
        // statistics
        $router->get('/statistics', function() {
            echo "Admin - Statistics";
        });
    });
};