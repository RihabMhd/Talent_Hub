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
        
        // Dashboard
        $router->get('/dashboard', function() use ($controllers) {
            $controllers['recruiterDashboard']->index();
        });
        
        // Job Offers Management
        $router->get('/jobs', function() use ($controllers) {
            $controllers['recruiterJobOffer']->index();
        });
        
        // Create job offer
        $router->post('/jobs/store', function() use ($controllers) {
            $controllers['recruiterJobOffer']->store();
        });
        
        // Update job offer
        $router->post('/jobs/{id}/update', function($id) use ($controllers) {
            $controllers['recruiterJobOffer']->update($id);
        });
        
        // Archive job offer
        $router->post('/jobs/{id}/archive', function($id) use ($controllers) {
            $controllers['recruiterJobOffer']->archive($id);
        });
        
        // Restore job offer
        $router->post('/jobs/{id}/restore', function($id) use ($controllers) {
            $controllers['recruiterJobOffer']->restore($id);
        });
        
        // Delete job offer
        $router->post('/jobs/{id}/delete', function($id) use ($controllers) {
            $controllers['recruiterJobOffer']->delete($id);
        });
        
        // Applications Management
        $router->get('/applications', function() {
            echo "Recruiter - All Applications";
        });
        
        $router->get('/jobs/{id}/applications', function($id) {
            echo "Recruiter - Job Applications for ID: " . $id;
        });
        
        $router->get('/applications/{id}', function($id) {
            echo "Recruiter - View Application ID: " . $id;
        });
        
        $router->post('/applications/{id}/accept', function($id) {
            echo "Recruiter - Accept Application ID: " . $id;
        });
        
        $router->post('/applications/{id}/reject', function($id) {
            echo "Recruiter - Reject Application ID: " . $id;
        });
        
        // Company Profile
        $router->get('/company', function() {
            echo "Recruiter - Company Profile";
        });
        
        $router->post('/company', function() {
            echo "Recruiter - Update Company";
        });
    });
};