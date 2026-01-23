<?php

use App\Controllers\Admin\ApplicationController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\JobOfferController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\TagController;
use App\Controllers\Candidate\ApplicationController as CandidateApplicationController;
use App\Controllers\Candidate\JobController as CandidateJobController;
use App\Controllers\Candidate\ProfileController;
use App\Controllers\Recruiter\ApplicationController as RecruiterApplicationController;
use App\Controllers\Recruiter\RecruiterDashboardController;
use App\Controllers\Recruiter\JobOfferController as RecruiterJobOfferController;
use App\Controllers\AuthController;
use App\Controllers\Admin\StatisticsController;
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
    $applicationRepository=new ApplicationRepository($db);
    $companyRepository = new \App\Repository\CompanyRepository($db);

    // Initialize Services
    $categoryService = new CategoryService($categoryRepository);
    $tagService = new TagService($tagRepository);
    $userService = new UserService($userRepository);
    $jobOfferService = new JobOfferService($jobOfferRepository);
    $applicationService = new ApplicationService($applicationRepository);

    return [
        // Admin Controllers
        'adminDashboard' => new AdminDashboardController($twig, $db),
        'category' => new CategoryController($categoryService, $twig),
        'tag' => new TagController($tagService, $twig),
        'adminUser' => new AdminUserController($verificationService, $twig),
        'user' => new UserController($userService, $twig),
        'adminJobOffer' => new \App\Controllers\Admin\JobOfferController(),
        'adminApplications' => new ApplicationController($applicationService, $twig),
        'adminStatistics' => new StatisticsController($twig, $db),

        // Recruiter Controllers
        'recruiterDashboard' => new RecruiterDashboardController($twig, $db),
        'recruiterJobOffer' => new RecruiterJobOfferController($jobOfferService, $categoryService, $tagService, $twig),
        'recruiterApplications' => new RecruiterApplicationController($applicationService, $twig),
        'recruiterProfile' => new \App\Controllers\Recruiter\ProfileController($companyRepository, $twig),
        // Candidate Controllers
        'candidateProfile' => new ProfileController($userService, $twig),
        // 'candidateApplications' => new ApplicationController($applicationService, $twig),

    ];
};