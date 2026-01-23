<?php
namespace App\Controllers\Candidate;

use App\Repository\ApplicationRepository;
use App\Config\Twig; // Ensure Twig is imported for the new method

class ApplicationController {
    
    private $appRepo;

    public function __construct() {
        $this->appRepo = new ApplicationRepository();
    }

    // [NEW] View My Applications Page
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Security Check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // 2. Fetch all applications for this user
        $applications = $this->appRepo->findByUserId($userId);

        // 3. Render the View
        echo Twig::render('candidate/applications.twig', [
            'applications' => $applications,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    public function apply($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Security Check: Must be logged in and be a candidate (role_id = 3)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            $_SESSION['error'] = "You must be logged in as a candidate to apply.";
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // 2. Check for Duplicate Application
        if ($this->appRepo->hasApplied($userId, $id)) {
            $_SESSION['error'] = "You have already applied for this job.";
            header("Location: /jobs/$id");
            exit;
        }

        // 3. Create Application
        if ($this->appRepo->create($userId, $id)) {
            $_SESSION['success'] = "Application submitted successfully! Good luck.";
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
        }
        
        // 4. Redirect back to the job page
        header("Location: /jobs/$id");
        exit;
    }
}