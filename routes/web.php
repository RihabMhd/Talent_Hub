<?php

use App\Config\Router;
use App\Controllers\AuthController;

return function(Router $router, $controllers) {
    $authController = $controllers['auth'];
    
    // home/Login routes
    $router->get('/', [$authController, 'showLoginForm']);
    $router->get('/login', [$authController, 'showLoginForm']);
    $router->post('/login', [$authController, 'login']);
    
    // registration routes
    $router->get('/register', [$authController, 'showRegisterForm']);
    $router->post('/register', [$authController, 'register']);
    
    // logout
    $router->get('/logout', [$authController, 'logout']);
    $router->get('/jobs', function() use ($controllers) {
        $controllers['job']->index();
    });
    $router->post('/candidate/applications/apply/{id}', [
    $controllers['candidateApplication'], 'apply'
]);
    $router->get('/jobs/{id}', function($id) use ($controllers) {
    $controllers['job']->show($id);
});
    
    // 403 Forbidden page
    $router->get('/403', function() {
        http_response_code(403);
        echo '<h1>403 - Access Forbidden</h1><p>You do not have permission to access this resource.</p>';
    });
};