<?php
namespace App\Controllers\Candidate;

use App\Repository\ApplicationRepository;
use App\Config\Twig;

class ApplicationController {
    
    private $appRepo;

    public function __construct() {
        $this->appRepo = new ApplicationRepository();
    }

    // afficher liste dial candidatures dyal candidate
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // n checkew ila user howa candidate 
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // njibou toutes les candidatures dial had user
        $applications = $this->appRepo->findByUserId($userId);

        // nrenderiw view
        echo Twig::render('candidate/applications.twig', [
            'applications' => $applications,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    // postuler l job offer
    public function apply($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // protection - khass ykon connecté w role candidate
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            $_SESSION['error'] = "You must be logged in as a candidate to apply.";
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // n checkew ila deja postuléé l had offre - ma n9derch npostuli 2 fois
        if ($this->appRepo->hasApplied($userId, $id)) {
            $_SESSION['error'] = "You have already applied for this job.";
            header("Location: /jobs/$id");
            exit;
        }

        // n créew candidature f database
        if ($this->appRepo->create($userId, $id)) {
            $_SESSION['success'] = "Application submitted successfully! Good luck.";
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
        }
        
        // nrja3 l page dial job offer
        header("Location: /jobs/$id");
        exit;
    }
}