<?php

use App\Config\Router;

return function(Router $router, $controllers, $middlewares) {
    
    // API routes group
    $router->group([
        'prefix' => '/api'
    ], function($router) use ($controllers, $middlewares) {
        
        // public API endpoints
        $router->get('/jobs', function() {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => []
            ]);
        });
        
        $router->get('/jobs/{id}', function() {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => []
            ]);
        });
        
        // protected API endpoints (require authentication)
        $authMiddleware = $middlewares['auth'];
        
        $router->post('/applications', function() {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Application submitted'
            ]);
        }, [$authMiddleware]);
        
        $router->get('/profile', function() {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $_SESSION['user'] ?? null
            ]);
        }, [$authMiddleware]);
    });
};