<?php
namespace App\Controllers\Candidate;
use App\Repository\ApplicationRepository; // 1. Import this

use App\Repository\CandidateRepository;
use App\Config\Twig;

class ProfileController {
    
    private $candidateRepo;
    private $appRepo;

    public function __construct() {
        $this->candidateRepo = new CandidateRepository();
        $this->appRepo = new ApplicationRepository();
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'];

        // 1. Get Stats
        $stats = $this->appRepo->getCandidateStats($userId);

        // 2. Get Recent Applications
        $recentApps = $this->appRepo->getRecentApplications($userId);

        // 3. Render View
        echo \App\Config\Twig::render('candidate/dashboard.twig', [
            'stats' => $stats,
            'recentApps' => $recentApps,
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'],
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