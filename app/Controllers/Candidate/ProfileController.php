<?php
namespace App\Controllers\Candidate;
use App\Repository\ApplicationRepository; // 1. Import this

use App\Repository\CandidateRepository;
use App\Repository\JobOfferRepository;
use App\Config\Twig;

class ProfileController {
    
    private $candidateRepo;
    private $appRepo;
    private $jobRepo;

    public function __construct() {
        $this->candidateRepo = new CandidateRepository();
        $this->appRepo = new ApplicationRepository();
        $this->jobRepo = new JobOfferRepository();
    }

    public function dashboard() {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        // Get Stats & Recent Applications (Existing code)
        $stats = $this->appRepo->getCandidateStats($userId);
        $recentApps = $this->appRepo->getRecentApplications($userId);

        // --- NEW RECOMMENDATION LOGIC ---
        $recommendedJobs = [];
        
        // 1. Get Candidate Profile to access skills
        $profile = $this->candidateRepo->findByUserId($userId);
        
        if (!empty($profile['skills'])) {
            // 2. Convert "HTML, CSS, JS" string into array ['HTML', 'CSS', 'JS']
            $skillsArray = explode(',', $profile['skills']);
            
            // 3. Trim whitespace from each skill
            $skillsArray = array_map('trim', $skillsArray);
            
            // 4. Fetch matching jobs
            $recommendedJobs = $this->jobRepo->findRecommended($skillsArray);
        }
        // --------------------------------

        echo Twig::render('candidate/dashboard.twig', [
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'],
            'stats' => $stats,
            'recentApps' => $recentApps,
            'recommendedJobs' => $recommendedJobs, // <--- Pass to View
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    // [NEW] View Profile (Read Only)
    public function show() {
        $this->checkCandidate();
        
        $userId = $_SESSION['user']['id'];
        $profile = $this->candidateRepo->findByUserId($userId);

        echo Twig::render('candidate/show.twig', [
            'profile' => $profile,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    // [UPDATED] Show Edit Form
    public function edit() {
        $this->checkCandidate();
        
        $userId = $_SESSION['user']['id'];
        $profile = $this->candidateRepo->findByUserId($userId);
        $allTags = $this->candidateRepo->getAllTags();
        $selectedTagIds = array_column($profile['tags'] ?? [], 'id');

        echo Twig::render('candidate/edit.twig', [
            'profile' => $profile,
            'allTags' => $allTags,
            'selectedTagIds' => $selectedTagIds,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    public function update() {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'titre' => $_POST['titre'],
                'telephone' => $_POST['telephone'],
                'adresse' => $_POST['adresse'],
                'salaire_min' => $_POST['salaire_min'] ?: null,
                'disponibilite' => $_POST['disponibilite'],
                'experience' => $_POST['experience']
            ];
            $tags = $_POST['tags'] ?? []; 

            if ($this->candidateRepo->updateProfile($userId, $data, $tags)) {
                $_SESSION['success'] = "Profile updated successfully!";
                $_SESSION['user']['name'] = $data['nom'] . ' ' . $data['prenom'];
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }

            // Redirect back to the View page
            header('Location: /candidate/profile');
            exit();
        }
    }

    private function checkCandidate() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 3) {
            header('Location: /login');
            exit();
        }
    }
}