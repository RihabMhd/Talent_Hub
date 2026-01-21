<?php

use App\Controllers\Admin\ApplicationController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\JobOfferController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\TagController;
use App\Controllers\Candidate\ApplicationController as CandidateApplicationController;
use App\Controllers\Candidate\JobController as CandidateJobController;
use App\Controllers\Candidate\ProfileController;
use App\Controllers\Recruiter\ApplicationController as RecruiterApplicationController;
use App\Controllers\Recruiter\DashboardController as RecruiterDashboardController;
use App\Controllers\Recruiter\JobOfferController as RecruiterJobOfferController;
use App\Controllers\AuthController;
use App\Controllers\Admin\AdminUserController;
use App\Services\UserVerificationService;
use App\Repository\ApplicationRepository;
use App\Repository\CategoryRepository;
use App\Repository\JobOfferRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;

use App\Services\ApplicationService;
use App\Services\CategoryService;
use App\Services\JobOfferService;
use App\Services\TagService;
use App\Services\UserService;

return function($twig, $db) {
    // Initialize Repositories
    $categoryRepository = new CategoryRepository($db);
    $tagRepository = new TagRepository($db);
    $userRepository = new UserRepository($db);
    $jobOfferRepository = new JobOfferRepository($db);
    $verificationService = new UserVerificationService($userRepository);
    $userRepository = new UserRepository($db);
    // $applicationRepository = new ApplicationRepository($db);

    // Initialize Services
    $categoryService = new CategoryService($categoryRepository);
    $tagService = new TagService($tagRepository);
    $userService = new UserService($userRepository);
    $jobOfferService = new JobOfferService($jobOfferRepository);
    // $applicationService = new ApplicationService($applicationRepository);

    return [
        // Admin Controllers
        'dashboard' => new DashboardController($twig, $db),
        'category' => new CategoryController($categoryService, $twig),
        'tag' => new TagController($tagService, $twig),
        'adminUser' => new AdminUserController($verificationService, $twig),
        'user' => new UserController($userService, $twig),
        'job' => new \App\Controllers\Admin\JobOfferController(),
        // 'application' => new ApplicationController($applicationService, $twig),
        // 'role' => new RoleController($twig, $db),
        // 'statistics' => new StatisticsController($twig, $db),

        // Candidate Controllers
        // 'candidate_profile' => new ProfileController($userService, $twig),
        // 'candidate_jobs' => new CandidateJobController($jobOfferService, $twig),
        // 'candidate_applications' => new CandidateApplicationController($applicationService, $twig),

        // // Recruiter Controllers
        // 'recruiter_dashboard' => new RecruiterDashboardController($twig, $db),
        // 'recruiter_jobs' => new RecruiterJobOfferController($jobOfferService, $categoryService, $tagService, $twig),
        // 'recruiter_applications' => new RecruiterApplicationController($applicationService, $twig),

        // // Auth Controller
        // 'auth' => new AuthController($userService, $twig),
    ];
};