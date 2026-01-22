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
$twig->addFunction(new \Twig\TwigFunction('url', function ($path) {
    return '/' . ltrim($path, '/');
}));

// initialize repositories
$userRepository = new UserRepository($db);

// initialize services
$validatorService = new ValidatorService();
$authService = new AuthService($userRepository);

<<<<<<< HEAD
// --- INITIALIZE CONTROLLERS ---
// 1. Start with your manual definitions
$controllers = [
    'auth' => new AuthController($authService, $validatorService),
    'jobOffer' => new JobOfferController(),
    'statistics' => new StatisticsController(),
    'applications' => new ApplicationController(),
    'candidateProfile' => new ProfileController()
];

// 2. Load extra controllers from config (if any)
if (file_exists(__DIR__ . '/../app/Config/controllers.php')) {
    $controllerLoader = require __DIR__ . '/../app/Config/controllers.php';
    $extraControllers = $controllerLoader($twig, $db);
 
    $controllers = array_merge($extraControllers, $controllers);
}
=======
// Load all controllers from controllers.php
$controllerLoader = require __DIR__ . '/../app/Config/controllers.php';
$controllers = $controllerLoader($twig, $db);

// Add auth controller and other specific controllers
$controllers['auth'] = new AuthController($authService, $validatorService);
$controllers['adminJobOffer'] = new JobOfferController();
$controllers['adminStatistics'] = new StatisticsController();
$controllers['adminApplications'] = new ApplicationController();
>>>>>>> parent of 6cc9305 (Merge pull request #16 from RihabMhd/feature/candidateProfile)

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
$router->get('/admin/offers', function () use ($controllers) {
    $controllers['adminJobOffer']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/offers/archive/(\d+)', function ($id) use ($controllers) {
    $controllers['adminJobOffer']->archive($id);
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/offers/restore/(\d+)', function ($id) use ($controllers) {
    $controllers['adminJobOffer']->restore($id);
}, [$middlewares['auth'], $middlewares['admin']]);


// --- Admin Statistics Routes ---
$router->get('/admin/statistics', function () use ($controllers) {
    $controllers['adminStatistics']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/statistics/export', function () use ($controllers) {
    $controllers['adminStatistics']->export();
}, [$middlewares['auth'], $middlewares['admin']]);


// Admin Application Management Routes
$router->get('/admin/applications', function () use ($controllers) {
    $controllers['adminApplications']->index();
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/applications/block/(\d+)', function ($id) use ($controllers) {
    $controllers['adminApplications']->blockCandidate($id);
}, [$middlewares['auth'], $middlewares['admin']]);

$router->get('/admin/applications/unblock/(\d+)', function ($id) use ($controllers) {
    $controllers['adminApplications']->unblockCandidate($id);
}, [$middlewares['auth'], $middlewares['admin']]);


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
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$router->dispatch($requestMethod, $requestUri);