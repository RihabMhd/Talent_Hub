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
use App\Controllers\Admin\ApplicationController;
use App\Controllers\Candidate\ProfileController; // Import Candidate Controller
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// initialize database connection
$database = new Database();
$db = $database->getConnection();

// initialize twig (Used by Admin Controllers via controllers.php)
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../app/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());
$twig->addFunction(new \Twig\TwigFunction('url', function ($path) {
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
    'applications' => new ApplicationController(),
    'candidateProfile' => new ProfileController() // Register Candidate Controller
];

// load admin controllers (legacy loader)
$adminControllerLoader = require __DIR__ . '/../app/Config/controllers.php';
$adminControllers = $adminControllerLoader($twig, $db);
$controllers = array_merge($controllers, $adminControllers);
// Load all controllers from controllers.php
$controllerLoader = require __DIR__ . '/../app/Config/controllers.php';
$controllers = $controllerLoader($twig, $db);

// Add auth controller and other specific controllers
$controllers['auth'] = new AuthController($authService, $validatorService);
$controllers['adminJobOffer'] = new JobOfferController();
$controllers['adminStatistics'] = new StatisticsController();
$controllers['adminApplications'] = new ApplicationController();

// initialize middlewares
$middlewares = [
    'auth' => new AuthMiddleware(),
    'recruiter' => new RoleMiddleware(['recruiter', 'recruteur']), 
    'admin' => new RoleMiddleware(['admin']),
    'candidate' => new RoleMiddleware(['candidate', 'candidat'])
];

// create router instance
$router = new Router();

// load route files
// Make sure the file names in your folder match these EXACTLY
$routeFiles = [
    __DIR__ . '/../routes/web.php',
    __DIR__ . '/../routes/admin.php',      // Handles all /admin routes
    __DIR__ . '/../routes/recruiter.php',
    __DIR__ . '/../routes/candidate.php',  // Ensure file is named 'candidate.php'
    __DIR__ . '/../routes/api.php'
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        $routeLoader = require $file;
        if (is_callable($routeLoader)) {
            $routeLoader($router, $controllers, $middlewares);
        }
    } else {
        // Optional: Debugging line to see which file is missing
        // echo "Warning: Route file not found: " . $file . "<br>";
    }
}

// Protected routes - Dashboard redirect
$router->get('/dashboard', function () use ($authService) {
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
$router->get('/change-password', function () use ($controllers) {
    $controllers['auth']->showChangePasswordForm();
}, [$middlewares['auth']]);

$router->post('/change-password', function () use ($controllers) {
    $controllers['auth']->changePassword();
}, [$middlewares['auth']]);

// dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);