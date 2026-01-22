<?php
namespace App\Controllers\Candidate;

use App\Repository\ApplicationRepository;

class ApplicationController {
    
    private $appRepo;

    public function __construct() {
        $this->appRepo = new ApplicationRepository();
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