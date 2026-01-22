<?php

use App\Config\Router;
use App\Config\Twig;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

return function(Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $candidateMiddleware = $middlewares['candidate'];
    
    // candidate routes group
    $router->group([
        'prefix' => '/candidate',
        'middlewares' => [$authMiddleware, $candidateMiddleware]
    ], function($router) use ($controllers) {
        
        // dashboard
        $router->get('/dashboard', function() {
            Twig::render('candidate/dashboard.twig');
        });
        
        // browse Jobs
        $router->get('/jobs', function() {
            echo "Candidate - Browse Jobs";
        });
        
        $router->get('/jobs/{id}', function() {
            echo "Candidate - View Job Details";
        });
        
        // applications
        $router->get('/applications', function() {
            echo "Candidate - My Applications";
        });
        
        $router->post('/jobs/{id}/apply', function() {
            echo "Candidate - Apply to Job";
        });
        
        $router->get('/applications/{id}', function() {
            echo "Candidate - View Application";
        });
        
        $router->post('/applications/{id}/cancel', function() {
            echo "Candidate - Cancel Application";
        });
        
        // profile Management
        $router->get('/profile', function() {
            echo "Candidate - View Profile";
        });
        
        $router->get('/profile/edit', function() {
            echo "Candidate - Edit Profile";
        });
        
        $router->post('/profile', function() {
            echo "Candidate - Update Profile";
        });
        
        $router->post('/profile/cv', function() {
            echo "Candidate - Upload CV";
        });
        
        $router->post('/profile/avatar', function() {
            echo "Candidate - Upload Avatar";
        });
        
        // recommendations
        $router->get('/recommendations', function() {
            echo "Candidate - Job Recommendations";
        });
    });
};