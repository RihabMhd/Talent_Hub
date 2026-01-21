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
            $controllers['dashboard']->index();
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
        // [FIX] Changed 'job' to 'jobOffer' to match index.php
        $router->get('/jobs', function() use ($controllers) {
            $controllers['jobOffer']->index();
        });
        
        $router->get('/jobs/create', function() use ($controllers) {
            $controllers['jobOffer']->create();
        });
        
        $router->post('/jobs', function() use ($controllers) {
            $controllers['jobOffer']->store();
        });
        
        $router->get('/jobs/{id}/edit', function($id) use ($controllers) {
            $controllers['jobOffer']->edit($id);
        });
        
        $router->post('/jobs/{id}', function($id) use ($controllers) {
            $controllers['jobOffer']->update($id);
        });
        
        // [FIX] Changed to match the archive method signature
        $router->post('/jobs/{id}/archive', function($id) use ($controllers) {
            $controllers['jobOffer']->archive($id);
        });
        
        $router->post('/jobs/{id}/restore', function($id) use ($controllers) {
            $controllers['jobOffer']->restore($id);
        });
        
        $router->post('/jobs/{id}/delete', function($id) use ($controllers) {
            $controllers['jobOffer']->destroy($id);
        });
        
        // --- Applications Management ---
        // [FIX] Changed 'application' to 'applications' (plural) to match index.php
        $router->get('/applications', function() use ($controllers) {
            $controllers['applications']->index();
        });
        
        // [FIX] Updated to use blockCandidate/unblockCandidate methods
        $router->get('/applications/block/{id}', function($id) use ($controllers) {
            $controllers['applications']->blockCandidate($id);
        });
        
        $router->get('/applications/unblock/{id}', function($id) use ($controllers) {
            $controllers['applications']->unblockCandidate($id);
        });
        
        // --- Statistics ---
        $router->get('/statistics', function() use ($controllers) {
            $controllers['statistics']->index();
        });
        
        $router->get('/statistics/export', function() use ($controllers) {
            $controllers['statistics']->export();
        });
        
        // --- Roles Management ---
        $router->get('/roles', function() use ($controllers) {
            $controllers['role']->index();
        });
        
        $router->post('/roles/store', function() use ($controllers) {
            $controllers['role']->store();
        });
        
        $router->post('/roles/update/{id}', function($id) use ($controllers) {
            $controllers['role']->update($id);
        });
        
        $router->post('/roles/delete/{id}', function($id) use ($controllers) {
            $controllers['role']->destroy($id);
        });
    });
};