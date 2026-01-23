<?php
namespace App\Controllers;

use App\Repository\JobOfferRepository;
use App\Repository\ApplicationRepository; // 1. Import the Application Repository
use App\Config\Twig;

class JobController {
    
    private $jobRepo;
    private $appRepo; // 2. Add property for Application Repository

    public function __construct() {
        $this->jobRepo = new JobOfferRepository();
        $this->appRepo = new ApplicationRepository(); // 3. Initialize it
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

        // 4. [NEW] Check if user has already applied
        // We only check if a user is logged in AND is a candidate (role_id = 3)
        $hasApplied = false;
        if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3) {
            $hasApplied = $this->appRepo->hasApplied($_SESSION['user']['id'], $id);
        }

        echo Twig::render('jobs/show.twig', [
            'offer' => $offer,
            'tags' => $tags,
            'session' => $_SESSION,
            'flash' => $flash, 
            'has_applied' => $hasApplied, // 5. Pass the result to Twig
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check for search query
        $keyword = $_GET['q'] ?? null;

        if ($keyword) {
            // If searching, use the search method
            $jobs = $this->jobRepo->searchActive($keyword);
        } else {
            // Otherwise, get all active jobs
            $jobs = $this->jobRepo->findAllActive();
        }

        echo Twig::render('jobs/index.twig', [
            'jobs' => $jobs,
            'search_query' => $keyword, // Pass this so we can keep the text in the box
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }
    
}