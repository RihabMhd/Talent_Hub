<?php
namespace App\Controllers\Admin;

use App\Repository\ApplicationRepository; 
use App\Config\Twig; 

class ApplicationController {

    private $applicationRepository;

    public function __construct() {
        $this->applicationRepository = new ApplicationRepository();
    }

    public function index() {
        $this->checkAdmin();

        // Get applications with details
        $applications = $this->applicationRepository->findAllWithDetails();
        
        // Get stats - ensure proper data structure
        $stats = $this->applicationRepository->getStatusStats();
        
        // Debug: Log both stats and applications to see what's being returned
        error_log("=== DEBUG INFO ===");
        error_log("Stats count: " . count($stats));
        error_log("Stats data: " . print_r($stats, true));
        error_log("Applications count: " . count($applications));
        error_log("Applications sample: " . print_r(array_slice($applications, 0, 2), true));
        error_log("==================");
        
        // Ensure both are arrays even if empty
        if (!is_array($stats)) {
            $stats = [];
        }
        if (!is_array($applications)) {
            $applications = [];
        }

        echo Twig::render('admin/applications/index.twig', [
            'applications' => $applications,
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    public function blockCandidate($userId) {
        $this->checkAdmin();
        if ($userId) {
            $this->applicationRepository->blockUser($userId);
        }
        header('Location: /admin/applications');
        exit();
    }

    public function unblockCandidate($userId) {
        $this->checkAdmin();
        if ($userId) {
            $this->applicationRepository->unblockUser($userId);
        }
        header('Location: /admin/applications');
        exit();
    }

    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 1) {
            header('Location: /login');
            exit();
        }
    }
}