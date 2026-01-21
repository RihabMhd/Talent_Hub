<?php
// start session
session_start();

// autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Config\Router;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Services\ValidatorService;
use App\Controllers\AuthController;
use App\Controllers\Admin\JobOfferController;
use App\Controllers\Admin\StatisticsController;
// [NEW 1] Import the ApplicationController
use App\Controllers\Admin\ApplicationController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// initialize database connection
$database = new Database();
$db = $database->getConnection();

// initialize twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../app/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());
$twig->addFunction(new \Twig\TwigFunction('url', function($path) {
    return '/' . ltrim($path, '/');
}));

// initialize repositories
$userRepository = new UserRepository($db);

// initialize services
$validatorService = new ValidatorService();
$authService = new AuthService($userRepository);

// initialize controllers
$controllers = [
    'auth' => new AuthController($authService, $validatorService),
    'jobOffer' => new JobOfferController(),
    'statistics' => new StatisticsController(),
    // [NEW 2] Initialize the ApplicationController
    'applications' => new ApplicationController()
];

// load admin controllers
$adminControllerLoader = require __DIR__ . '/../app/Config/controllers.php';
$adminControllers = $adminControllerLoader($twig, $db);
$controllers = array_merge($controllers, $adminControllers);

// initialize middlewares
$middlewares = [
    'auth' => new AuthMiddleware(),
    'admin' => new RoleMiddleware(['admin']),
    'recruiter' => new RoleMiddleware(['recruiter', 'recruteur']),
    'candidate' => new RoleMiddleware(['candidate', 'candidat'])
];

// create router instance
$router = new Router();

// load route files
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

// --- Admin Job Offer Routes ---
$router->get('/admin/offers', function() use ($controllers) {
    $controllers['jobOffer']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/offers/archive/(\d+)', function($id) use ($controllers) {
    $controllers['jobOffer']->archive($id);
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/offers/restore/(\d+)', function($id) use ($controllers) {
    $controllers['jobOffer']->restore($id);
}, [$middlewares['auth'], $middlewares['admin']]);


// --- Admin Statistics Routes ---
$router->get('/admin/statistics', function() use ($controllers) {
    $controllers['statistics']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/statistics/export', function() use ($controllers) {
    $controllers['statistics']->export();
}, [$middlewares['auth'], $middlewares['admin']]);


// [NEW 3] Admin Application Management Routes
$router->get('/admin/applications', function() use ($controllers) {
    $controllers['applications']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/applications/block/(\d+)', function($id) use ($controllers) {
    $controllers['applications']->blockCandidate($id);
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/applications/unblock/(\d+)', function($id) use ($controllers) {
    $controllers['applications']->unblockCandidate($id);
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
    
    // redirect based on role
    switch ($user['role_id']) {
        case 1:
            header('Location: /admin/dashboard'); 
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

// change password routes
$router->get('/change-password', function() use ($controllers) {
    $controllers['auth']->showChangePasswordForm();
}, [$middlewares['auth']]);

$router->post('/change-password', function() use ($controllers) {
    $controllers['auth']->changePassword();
}, [$middlewares['auth']]);

// dispatch the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$router->dispatch($requestMethod, $requestUri);