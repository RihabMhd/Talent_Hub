<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Config\Router;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Services\ValidatorService;
use App\Controllers\AuthController;
use App\Controllers\JobController;
use App\Controllers\Admin\JobOfferController;
use App\Controllers\Admin\StatisticsController;
use App\Controllers\Admin\ApplicationController;
use App\Controllers\Candidate\ProfileController; 
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Controllers\Candidate\ApplicationController as CandidateAppController;


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

// --- INITIALIZE REPOSITORIES ---
$userRepository = new UserRepository($db);

// --- INITIALIZE SERVICES ---
$validatorService = new ValidatorService();
$authService = new AuthService($userRepository);

// --- INITIALIZE CONTROLLERS ---
$controllers = [
    'auth' => new AuthController($authService, $validatorService),
    'jobOffer' => new JobOfferController(),
    'statistics' => new StatisticsController(),
    'applications' => new ApplicationController(),
    'candidateProfile' => new ProfileController(),
    'job' => new JobController(),
    'candidateApplication' => new CandidateAppController()
];

if (file_exists(__DIR__ . '/../app/Config/controllers.php')) {
    $controllerLoader = require __DIR__ . '/../app/Config/controllers.php';
    $extraControllers = $controllerLoader($twig, $db);
 
    $controllers = array_merge($extraControllers, $controllers);
}

// initialize middlewares
$middlewares = [
    'auth' => new AuthMiddleware(),
    'recruiter' => new RoleMiddleware(['recruiter', 'recruteur']), 
    'admin' => new RoleMiddleware(['admin']),
    'candidate' => new RoleMiddleware(['candidate', 'candidat'])
];


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

// protected routes
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