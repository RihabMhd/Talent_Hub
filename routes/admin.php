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
        
        // Alternative route for pending (keeps old links working)
        $router->get('/users/pending', function () use ($controllers) {
            $controllers['adminUser']->pending();
        });

        $router->post('/users/verify/{id}', function ($id) use ($controllers) {
            $controllers['adminUser']->verify($id);
        });

        $router->post('/users/reject/{id}', function ($id) use ($controllers) {
            $controllers['adminUser']->reject($id);
        });
        
        // --- Category Management ---
        $router->get('/categories', function() use ($controllers) {
            $controllers['category']->index();
        });

        $router->post('/categories/store', function () use ($controllers) {
            $controllers['category']->store();
        });

        $router->post('/categories/update/{id}', function ($id) use ($controllers) {
            $controllers['category']->update($id);
        });

        $router->post('/categories/delete/{id}', function ($id) use ($controllers) {
            $controllers['category']->destroy($id);
        });
        
        // --- Tag Management ---
        $router->get('/tags', function() use ($controllers) {
            $controllers['tag']->index();
        });

        $router->post('/tags/store', function () use ($controllers) {
            $controllers['tag']->store();
        });

        $router->post('/tags/update/{id}', function ($id) use ($controllers) {
            $controllers['tag']->update($id);
        });

        $router->post('/tags/delete/{id}', function ($id) use ($controllers) {
            $controllers['tag']->destroy($id);
        });
        
        // --- Job Offers Management ---
        // [FIX] Key changed to 'jobOffer' to match index.php
        $router->get('/jobs', function() use ($controllers) {
            $controllers['jobOffer']->index();
        });

        // Update these too so they match the pattern
        $router->get('/jobs/archive/{id}', function($id) use ($controllers) {
            $controllers['jobOffer']->archive($id);
        });
        
        $router->get('/jobs/restore/{id}', function($id) use ($controllers) {
            $controllers['jobOffer']->restore($id);
        });
        
        // --- Applications Management ---
        // [FIX] Key changed to 'applications' (plural) to match index.php
        $router->get('/applications', function() use ($controllers) {
            $controllers['applications']->index();
        });
        
        // [FIX] Updated to use blockCandidate/unblockCandidate
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

        $router->post('/roles/store', function () use ($controllers) {
            $controllers['role']->store();
        });

        $router->post('/roles/update/{id}', function ($id) use ($controllers) {
            $controllers['role']->update($id);
        });

        $router->post('/roles/delete/{id}', function ($id) use ($controllers) {
            $controllers['role']->destroy($id);
        });
    });
};