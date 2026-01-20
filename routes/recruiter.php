<?php

use App\Config\Router;
use App\Config\Twig;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

return function(Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $recruiterMiddleware = $middlewares['recruiter'];
    
    // recruiter routes group
    $router->group([
        'prefix' => '/recruiter',
        'middlewares' => [$authMiddleware, $recruiterMiddleware]
    ], function($router) use ($controllers) {
        
        // dashboard
        $router->get('/dashboard', function() {
            Twig::render('recruiter/dashboard.twig');
        });
        
        // job Offers Management
        $router->get('/jobs', function() {
            echo "Recruiter - My Job Offers";
        });
        
        $router->get('/jobs/create', function() {
            echo "Recruiter - Create Job Offer";
        });
        
        $router->post('/jobs', function() {
            echo "Recruiter - Store Job Offer";
        });
        
        $router->get('/jobs/{id}', function() {
            echo "Recruiter - View Job Offer";
        });
        
        $router->get('/jobs/{id}/edit', function() {
            echo "Recruiter - Edit Job Offer";
        });
        
        $router->post('/jobs/{id}', function() {
            echo "Recruiter - Update Job Offer";
        });
        
        $router->post('/jobs/{id}/delete', function() {
            echo "Recruiter - Delete Job Offer";
        });
        
        // applications Management
        $router->get('/applications', function() {
            echo "Recruiter - All Applications";
        });
        
        $router->get('/jobs/{id}/applications', function() {
            echo "Recruiter - Job Applications";
        });
        
        $router->get('/applications/{id}', function() {
            echo "Recruiter - View Application";
        });
        
        $router->post('/applications/{id}/accept', function() {
            echo "Recruiter - Accept Application";
        });
        
        $router->post('/applications/{id}/reject', function() {
            echo "Recruiter - Reject Application";
        });
        
        // company Profile
        $router->get('/company', function() {
            echo "Recruiter - Company Profile";
        });
        
        $router->post('/company', function() {
            echo "Recruiter - Update Company";
        });
    });
};