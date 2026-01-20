<?php
namespace App\Controllers\Admin;

use App\Models\JobOffer;
use App\Config\Twig; 

class JobOfferController {

    private $jobOfferModel;

    public function __construct() {
        $this->jobOfferModel = new JobOffer();
    }

    public function index() {
        // 1. Security Check
        $this->checkAdmin();

        // 2. Get Data
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $offers = $this->jobOfferModel->findAllForAdmin($filter);
        $stats  = $this->jobOfferModel->countOffersPerRecruiter();

        // 3. Render View using Twig (The Fix)
        echo Twig::render('admin/offers/index.twig', [
            'offers' => $offers,
            'stats' => $stats,
            'currentFilter' => $filter,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI'] // Needed for the sidebar active link
        ]);
    }

    // --- Actions ---

    public function archive($id) {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferModel->softDelete($id);
        }
        header('Location: /admin/offers');
        exit();
    }

    public function restore($id) {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferModel->restore($id);
        }
        header('Location: /admin/offers');
        exit();
    }

    // --- Security Helper ---

    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ensure user is logged in AND is an Admin (Role ID 1)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 1) {
            header('Location: /login');
            exit();
        }
    }
}