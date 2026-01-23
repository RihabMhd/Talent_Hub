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

        // njibou les applications kamlin m3a details dyalhom (candidate, job offer...)
        $applications = $this->applicationRepository->findAllWithDetails();
        
        // stats dial status (pending, accepted, rejected...)
        $stats = $this->applicationRepository->getStatusStats();
        
        // double check ila data jat array, ila la nkhliha empty array
        // 7it ila jat null ghadi tcrashi page
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

    // bloquer candidate 
    public function blockCandidate($userId) {
        $this->checkAdmin();
        if ($userId) {
            $this->applicationRepository->blockUser($userId);
        }
        header('Location: /admin/applications');
        exit();
    }

    // débloquer candidate
    public function unblockCandidate($userId) {
        $this->checkAdmin();
        if ($userId) {
            $this->applicationRepository->unblockUser($userId);
        }
        header('Location: /admin/applications');
        exit();
    }

    // protection - ghir admin li y9der idkhel
    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 1) {
            header('Location: /login');
            exit();
        }
    }
}