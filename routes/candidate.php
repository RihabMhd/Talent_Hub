<?php

use App\Config\Router;

return function(Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];

    $router->group([
        'prefix' => '/candidate',
        'middlewares' => [$authMiddleware]
    ], function($router) use ($controllers) {

        // Dashboard
        $router->get('/dashboard', function() use ($controllers) {
            $controllers['candidateProfile']->dashboard();
        });

        // 1. View Profile (Read Only)
        $router->get('/profile', function() use ($controllers) {
            $controllers['candidateProfile']->show();
        });

        // 2. Show Edit Form
        $router->get('/profile/edit', function() use ($controllers) {
            $controllers['candidateProfile']->edit();
        });

        // 3. Process Update
        $router->post('/profile/update', function() use ($controllers) {
            $controllers['candidateProfile']->update();
        });
    });
};