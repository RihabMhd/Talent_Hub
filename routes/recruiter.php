<?php

use App\Config\Router;
use App\Config\Twig;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

return function (Router $router, $controllers, $middlewares) {
    $authMiddleware = $middlewares['auth'];
    $recruiterMiddleware = $middlewares['recruiter'];

    // recruiter routes group
    $router->group([
        'prefix' => '/recruiter',
        'middlewares' => [$authMiddleware, $recruiterMiddleware]
    ], function ($router) use ($controllers) {

        // Dashboard
        $router->get('/dashboard', function () use ($controllers) {
            $controllers['recruiterDashboard']->index();
        });

        // Job Offers Management
        $router->get('/jobs', function () use ($controllers) {
            $controllers['recruiterJobOffer']->index();
        });

        // Create job offer
        $router->post('/jobs/store', function () use ($controllers) {
            $controllers['recruiterJobOffer']->store();
        });

        // Update job offer
        $router->post('/jobs/{id}/update', function ($id) use ($controllers) {
            $controllers['recruiterJobOffer']->update($id);
        });

        // Archive job offer
        $router->post('/jobs/{id}/archive', function ($id) use ($controllers) {
            $controllers['recruiterJobOffer']->archive($id);
        });

        // Restore job offer
        $router->post('/jobs/{id}/restore', function ($id) use ($controllers) {
            $controllers['recruiterJobOffer']->restore($id);
        });

        // Delete job offer
        $router->post('/jobs/{id}/delete', function ($id) use ($controllers) {
            $controllers['recruiterJobOffer']->delete($id);
        });

        // View all applications for recruiter's job offers
        $router->get('/applications', function () use ($controllers) {
            $controllers['recruiterApplications']->index();
        });

        // Filter applications by status (en_attente, acceptee, refusee)
        $router->get('/applications/filter', function () use ($controllers) {
            $controllers['recruiterApplications']->filterByStatus();
        });

        // Search applications by candidate name/email
        $router->get('/applications/search', function () use ($controllers) {
            $controllers['recruiterApplications']->search();
        });

        // View specific candidate application details
        $router->get('/applications/view/{id}', function ($id) use ($controllers) {
            $controllers['recruiterApplications']->viewCandidate($id);
        });

        // Accept a candidate's application 
        $router->post('/applications/accepter/{id}', function ($id) use ($controllers) {
            $controllers['recruiterApplications']->accept($id);
        });

        // Reject a candidate's application 
        $router->post('/applications/refuser/{id}', function ($id) use ($controllers) {
            $controllers['recruiterApplications']->reject($id);
        });

        $router->get('/company', function () use ($controllers) {
            $controllers['recruiterProfile']->show();
        });

        $router->post('/company', function () use ($controllers) {
            $controllers['recruiterProfile']->update();
        });
    });
};
