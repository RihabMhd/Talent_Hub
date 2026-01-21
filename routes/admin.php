<?php

use App\Config\Router;
use App\Config\Twig;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

return function (Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $adminMiddleware = $middlewares['admin'];

    // admin routes group
    $router->group([
        'prefix' => '/admin',
        'middlewares' => [$authMiddleware, $adminMiddleware]
    ], function ($router) use ($controllers) {

        // dashboard
        $router->get('/dashboard', function () use ($controllers) {
            $controllers['dashboard']->index();
        });

        // Main users page shows pending verifications
        $router->get('/users', function () use ($controllers) {
            $controllers['adminUser']->pending();
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

        // Category Management
        $router->get('/categories', function () use ($controllers) {
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

        // Tag Management
        $router->get('/tags', function () use ($controllers) {
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

        // Job Offers Management
        $router->get('/jobs', function () use ($controllers) {
            $controllers['job']->index();
        });

        $router->get('/jobs/create', function () use ($controllers) {
            $controllers['job']->create();
        });

        $router->post('/jobs', function () use ($controllers) {
            $controllers['job']->store();
        });

        $router->get('/jobs/{id}/edit', function ($id) use ($controllers) {
            $controllers['job']->edit($id);
        });

        $router->post('/jobs/{id}', function ($id) use ($controllers) {
            $controllers['job']->update($id);
        });

        $router->post('/jobs/{id}/archive', function ($id) use ($controllers) {
            $controllers['job']->archive($id);
        });

        $router->post('/jobs/{id}/restore', function ($id) use ($controllers) {
            $controllers['job']->restore($id);
        });

        $router->post('/jobs/{id}/delete', function ($id) use ($controllers) {
            $controllers['job']->destroy($id);
        });

        // Applications Management
        $router->get('/applications', function () use ($controllers) {
            $controllers['application']->index();
        });

        $router->get('/applications/{id}', function ($id) use ($controllers) {
            $controllers['application']->show($id);
        });

        $router->post('/applications/{id}/approve', function ($id) use ($controllers) {
            $controllers['application']->approve($id);
        });

        $router->post('/applications/{id}/reject', function ($id) use ($controllers) {
            $controllers['application']->reject($id);
        });

        $router->post('/applications/{id}/delete', function ($id) use ($controllers) {
            $controllers['application']->destroy($id);
        });

        // Statistics
        $router->get('/statistics', function () use ($controllers) {
            $controllers['statistics']->index();
        });

        // Roles Management
        $router->get('/roles', function () use ($controllers) {
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