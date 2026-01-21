<?php

namespace App\Controllers\Admin;

use App\Models\JobOffer;
use App\Config\Twig;

class JobOfferController
{

    private $jobOfferModel;

    public function __construct()
    {
        $this->jobOfferModel = new JobOffer();
    }

    public function index()
    {
        // 1. Security Check
        $this->checkAdmin();

        // 2. Get Data
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $offers = $this->jobOfferModel->findAllForAdmin($filter);
        $stats  = $this->jobOfferModel->countOffersPerRecruiter();

        // 3. Render View using Twig 
        echo Twig::render('admin/jobs/index.html.twig', [
            'offers' => $offers,
            'stats' => $stats,
            'currentFilter' => $filter,
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'] ?? null,
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    // --- Actions ---

    public function archive($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferModel->softDelete($id);
            $_SESSION['success'] = 'Job offer archived successfully';
        }
        header('Location: /admin/jobs');
        exit();
    }

    public function restore($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferModel->restore($id);
            $_SESSION['success'] = 'Job offer restored successfully';
        }
        header('Location: /admin/jobs');
        exit();
    }

    public function create()
    {
        $this->checkAdmin();
        // Render create form
        echo Twig::render('admin/jobs/create.html.twig', [
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'] ?? null,
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
    }

    public function store()
    {
        $this->checkAdmin();
        // Handle job creation
        // Add your logic here
        $_SESSION['success'] = 'Job offer created successfully';
        header('Location: /admin/jobs');
        exit();
    }

    public function edit($id)
    {
        $this->checkAdmin();
        // Get job offer and render edit form
        $offer = $this->jobOfferModel->findById($id);

        if (!$offer) {
            $_SESSION['error'] = 'Job offer not found';
            header('Location: /admin/jobs');
            exit();
        }

        echo Twig::render('admin/jobs/edit.html.twig', [
            'offer' => $offer,
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'] ?? null,
            'app' => [
                'request' => [
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]
            ]
        ]);
    }

    public function update($id)
    {
        $this->checkAdmin();
        // Handle job update
        // Add your logic here
        $_SESSION['success'] = 'Job offer updated successfully';
        header('Location: /admin/jobs');
        exit();
    }

    public function destroy($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferModel->delete($id);
            $_SESSION['success'] = 'Job offer deleted successfully';
        }
        header('Location: /admin/jobs');
        exit();
    }

    // --- Security Helper ---

    private function checkAdmin()
    {
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
