<?php
namespace App\Controllers\Admin;

use App\Repository\ApplicationRepository; // [FIX] Use Repository
use App\Config\Twig; 

class ApplicationController {

    private $applicationRepository;

    public function __construct() {
        // [FIX] Initialize Repository
        $this->applicationRepository = new ApplicationRepository();
    }

    public function index() {
        $this->checkAdmin();

        // [FIX] Use repository methods
        $applications = $this->applicationRepository->findAllWithDetails();
        $stats = $this->applicationRepository->getStatusStats();

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