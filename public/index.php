<?php
// Start session
session_start();

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Config\Router;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Services\ValidatorService;
use App\Controllers\AuthController;
// [CHANGE 1] Import the JobOfferController
use App\Controllers\Admin\JobOfferController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize repositories
$userRepository = new UserRepository($db);

// Initialize services
$validatorService = new ValidatorService();
$authService = new AuthService($userRepository);

// Initialize controllers
$controllers = [
    'auth' => new AuthController($authService, $validatorService),
    // [CHANGE 2] Initialize the JobOfferController
    'jobOffer' => new JobOfferController() 
];

// Initialize middlewares
$middlewares = [
    'auth' => new AuthMiddleware(),
    'admin' => new RoleMiddleware(['admin']),
    'recruiter' => new RoleMiddleware(['recruiter', 'recruteur']),
    'candidate' => new RoleMiddleware(['candidate', 'candidat'])
];

// Create router instance
$router = new Router();

// Load route files
$routeFiles = [
    __DIR__ . '/../routes/web.php',
    __DIR__ . '/../routes/admin.php',
    __DIR__ . '/../routes/recruiter.php',
    __DIR__ . '/../routes/candidate.php',
    __DIR__ . '/../routes/api.php'
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        $routeLoader = require $file;
        if (is_callable($routeLoader)) {
            $routeLoader($router, $controllers, $middlewares);
        }
    }
}

// [CHANGE 3] Add Admin Job Offer Routes directly here 
// (Or better yet, move these into routes/admin.php if you want to be cleaner)

// 1. List Offers
$router->get('/admin/offers', function() use ($controllers) {
    $controllers['jobOffer']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

// 2. Archive Offer (assuming your Router supports regex like this)
$router->get('/admin/offers/archive/(\d+)', function($id) use ($controllers) {
    $controllers['jobOffer']->archive($id);
}, [$middlewares['auth'], $middlewares['admin']]);

// 3. Restore Offer
$router->get('/admin/offers/restore/(\d+)', function($id) use ($controllers) {
    $controllers['jobOffer']->restore($id);
}, [$middlewares['auth'], $middlewares['admin']]);


// Protected routes - Dashboard redirect
$router->get('/dashboard', function() use ($authService) {
    if (!$authService->isLoggedIn()) {
        header('Location: /login');
        exit;
    }
    
    $user = $authService->getCurrentUser();
    
    if (!$user || !isset($user['role_id'])) {
        $authService->logout();
        header('Location: /login');
        exit;
    }
    
    // Redirect based on role
    switch ($user['role_id']) {
        case 1:
            header('Location: /admin/dashboard'); // Ensure you have a route for this too!
            break;
        case 2:
            header('Location: /recruiter/dashboard');
            break;
        case 3:
            header('Location: /candidate/dashboard');
            break;
        default:
            header('Location: /login');
            break;
    }
    exit;
}, [$middlewares['auth']]);

// Change password routes
$router->get('/change-password', function() use ($controllers) {
    $controllers['auth']->showChangePasswordForm();
}, [$middlewares['auth']]);

$router->post('/change-password', function() use ($controllers) {
    $controllers['auth']->changePassword();
}, [$middlewares['auth']]);

// Dispatch the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$router->dispatch($requestMethod, $requestUri);