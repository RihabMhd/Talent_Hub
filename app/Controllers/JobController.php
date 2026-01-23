<?php
namespace App\Controllers;

use App\Repository\JobOfferRepository;
use App\Repository\ApplicationRepository;
use App\Config\Twig;

class JobController {
    
    private $jobRepo;
    private $appRepo;

    public function __construct() {
        $this->jobRepo = new JobOfferRepository();
        $this->appRepo = new ApplicationRepository();
    }

    // afficher détails dial job offer wa7ed
    public function show($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // njibou flash messages - success wla error mn session
        // hadi t affichaw mara wa7da w tmchi
        $flash = [];
        if (isset($_SESSION['success'])) {
            $flash['success'] = $_SESSION['success'];
            unset($_SESSION['success']); 
        }
        if (isset($_SESSION['error'])) {
            $flash['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        // njibou offer m3a company info w category
        $offer = $this->jobRepo->findFullOffer($id);

        if (!$offer) {
            // ila ma l9inach offer nrja3 l liste
            header('Location: /jobs'); 
            exit();
        }

        // njibou tags dial offer (skills, technologies...)
        $tags = $this->jobRepo->getTagsByOfferId($id);

        // n checkew ila user deja postula l had offer
        // hadi important bach n disabliw button apply
        $hasApplied = false;
        if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3) {
            $hasApplied = $this->appRepo->hasApplied($_SESSION['user']['id'], $id);
        }

        echo Twig::render('jobs/show.twig', [
            'offer' => $offer,
            'tags' => $tags,
            'session' => $_SESSION,
            'flash' => $flash, 
            'has_applied' => $hasApplied,  
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    // afficher liste dial job offers - m3a search functionality
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // n checkew ila kayn search query f URL
        $keyword = $_GET['q'] ?? null;

        if ($keyword) {
            // ila kayn search, nst3mlو search method
            $jobs = $this->jobRepo->searchActive($keyword);
        } else {
            // sinon njibou tous les active jobs
            $jobs = $this->jobRepo->findAllActive();
        }

        echo Twig::render('jobs/index.twig', [
            'jobs' => $jobs,
            'search_query' => $keyword,  
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }
    
}