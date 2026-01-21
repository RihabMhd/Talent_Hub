<?php

use App\Config\Router;

return function (Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $adminMiddleware = $middlewares['admin'];

    // admin routes group
    $router->group([
        'prefix' => '/admin',
        'middlewares' => [$authMiddleware, $adminMiddleware]
    ], function($router) use ($controllers) {
        
        // Dashboard
        $router->get('/dashboard', function() use ($controllers) {
            $controllers['adminDashboard']->index();
        });
        
        // --- User Management ---
        $router->get('/users', function() use ($controllers) {
            $controllers['user']->index();
        });
        
        $router->get('/users/create', function() use ($controllers) {
            $controllers['user']->create();
        });
        
        $router->post('/users', function() use ($controllers) {
            $controllers['user']->store();
        });
        
        $router->get('/users/{id}/edit', function($id) use ($controllers) {
            $controllers['user']->edit($id);
        });
        
        $router->post('/users/{id}', function($id) use ($controllers) {
            $controllers['user']->update($id);
        });
        
        $router->post('/users/{id}/delete', function($id) use ($controllers) {
            $controllers['user']->destroy($id);
        });
        
        // --- Category Management ---
        $router->get('/categories', function() use ($controllers) {
            $controllers['category']->index();
        });
        
        $router->post('/categories/store', function() use ($controllers) {
            $controllers['category']->store();
        });
        
        $router->post('/categories/update/{id}', function($id) use ($controllers) {
            $controllers['category']->update($id);
        });
        
        $router->post('/categories/delete/{id}', function($id) use ($controllers) {
            $controllers['category']->destroy($id);
        });
        
        // --- Tag Management ---
        $router->get('/tags', function() use ($controllers) {
            $controllers['tag']->index();
        });
        
        $router->post('/tags/store', function() use ($controllers) {
            $controllers['tag']->store();
        });
        
        $router->post('/tags/update/{id}', function($id) use ($controllers) {
            $controllers['tag']->update($id);
        });
        
        $router->post('/tags/delete/{id}', function($id) use ($controllers) {
            $controllers['tag']->destroy($id);
        });
        
        // --- Job Offers Management ---
        $router->get('/jobs', function() use ($controllers) {
            $controllers['adminJobOffer']->index();
        });
        
        $router->get('/jobs/create', function() use ($controllers) {
            $controllers['adminJobOffer']->create();
        });
        
        $router->post('/jobs', function() use ($controllers) {
            $controllers['adminJobOffer']->store();
        });
        
        $router->get('/jobs/{id}/edit', function($id) use ($controllers) {
            $controllers['adminJobOffer']->edit($id);
        });
        
        $router->post('/jobs/{id}', function($id) use ($controllers) {
            $controllers['adminJobOffer']->update($id);
        });
        
        $router->post('/jobs/{id}/archive', function($id) use ($controllers) {
            $controllers['adminJobOffer']->archive($id);
        });
        
        $router->post('/jobs/{id}/restore', function($id) use ($controllers) {
            $controllers['adminJobOffer']->restore($id);
        });
        
        $router->post('/jobs/{id}/delete', function($id) use ($controllers) {
            $controllers['adminJobOffer']->destroy($id);
        });
        
        // --- Applications Management ---
        $router->get('/applications', function() use ($controllers) {
            $controllers['adminApplications']->index();
        });
        
        $router->get('/applications/block/{id}', function($id) use ($controllers) {
            $controllers['adminApplications']->blockCandidate($id);
        });
        
        $router->get('/applications/unblock/{id}', function($id) use ($controllers) {
            $controllers['adminApplications']->unblockCandidate($id);
        });
        
        // --- Statistics ---
        $router->get('/statistics', function() use ($controllers) {
            $controllers['adminStatistics']->index();
        });
        
        $router->get('/statistics/export', function() use ($controllers) {
            $controllers['adminStatistics']->export();
        });
        
    });
};