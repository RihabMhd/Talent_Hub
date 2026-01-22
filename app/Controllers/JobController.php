<?php
namespace App\Controllers;

use App\Repository\JobOfferRepository;
use App\Config\Twig;

class JobController {
    
    private $jobRepo;

    public function __construct() {
        $this->jobRepo = new JobOfferRepository();
    }

    public function show($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // --- FLASH MESSAGE LOGIC START ---
        $flash = [];
        if (isset($_SESSION['success'])) {
            $flash['success'] = $_SESSION['success']; // Grab the message
            unset($_SESSION['success']);              // Delete it from session
        }
        if (isset($_SESSION['error'])) {
            $flash['error'] = $_SESSION['error'];     // Grab the message
            unset($_SESSION['error']);                // Delete it from session
        }
        // --- FLASH MESSAGE LOGIC END ---

        // 1. Fetch Offer Details
        $offer = $this->jobRepo->findFullOffer($id);

        if (!$offer) {
            header('Location: /jobs'); 
            exit();
        }

        $tags = $this->jobRepo->getTagsByOfferId($id);

        echo Twig::render('jobs/show.twig', [
            'offer' => $offer,
            'tags' => $tags,
            'session' => $_SESSION,
            'flash' => $flash, // <--- Pass the grabbed messages here
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Fetch all active jobs (Ensure you added findAllActive() to your Repository)
        $jobs = $this->jobRepo->findAllActive();

        // 2. Render the grid view
        echo Twig::render('jobs/index.twig', [
            'jobs' => $jobs,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }
    
}