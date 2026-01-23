<?php

namespace App\Controllers\Candidate;

use App\Repository\ApplicationRepository;
use App\Repository\CandidateRepository;
use App\Repository\JobOfferRepository;
use App\Config\Twig;

class ProfileController
{

    private $candidateRepo;
    private $appRepo;
    private $jobRepo;

    public function __construct()
    {
        $this->candidateRepo = new CandidateRepository();
        $this->appRepo = new ApplicationRepository();
        $this->jobRepo = new JobOfferRepository();
    }

    // dashboard dial candidate - kaychof stats w jobs recommandés
    public function dashboard()
    {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        // njibou stats - ch7al candidature envoyé, accepté, refusé...
        $stats = $this->appRepo->getCandidateStats($userId);
        
        // dernières candidatures li dar
        $recentApps = $this->appRepo->getRecentApplications($userId);

        // job recommendations based 3la skills dyalo
        $recommendedJobs = [];
        $profile = $this->candidateRepo->findByUserId($userId);

        // ila 3ndo skills f profile, ghan recommandiw jobs similar
        if (!empty($profile['skills'])) {
            // n splittiw skills string l array
            $skillsArray = explode(',', $profile['skills']);
            $skillsArray = array_map('trim', $skillsArray); 
            $recommendedJobs = $this->jobRepo->findRecommended($skillsArray);
        }

        echo Twig::render('candidate/dashboard.twig', [
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'],
            'stats' => $stats,
            'recentApps' => $recentApps,
            'recommendedJobs' => $recommendedJobs,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    // afficher profile dial candidate - read only
    public function show()
    {
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

    // form dial modification profile
    public function edit()
    {
        $this->checkCandidate();

        $userId = $_SESSION['user']['id'];
        $profile = $this->candidateRepo->findByUserId($userId);
        
        // njibou tous les tags disponibles (skills comme PHP, Laravel, React...)
        $allTags = $this->candidateRepo->getAllTags();
        
        // tags li deja selected 3nd candidate
        $selectedTagIds = array_column($profile['tags'] ?? [], 'id');

        echo Twig::render('candidate/edit.twig', [
            'profile' => $profile,
            'allTags' => $allTags,
            'selectedTagIds' => $selectedTagIds,
            'session' => $_SESSION,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
    }

    // sauvegarder modifications dial profile
    public function update()
    {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // n jm3ou data mn form - both users table o candidates table
            $data = [
                'nom'              => $_POST['nom'] ?? '',
                'prenom'           => $_POST['prenom'] ?? '',
                'telephone'        => $_POST['telephone'] ?? null,
                'skills'           => $_POST['skills'] ?? null,
                'experience_annee' => $_POST['experience_annee'] ?? 0,
                'expected_salary'  => $_POST['expected_salary'] ?? null,
            ];

            $tags = $_POST['tags'] ?? [];

            // upload CV ila candidate uploadا wa7ed jdid
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/cvs/';
                // n créew folder ila makanch
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                // n generateiw unique filename bach ma ykon conflict
                $fileName = uniqid() . '_' . basename($_FILES['cv']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                    $data['cv_path'] = $targetPath;
                }
            }

            if ($this->candidateRepo->updateProfile($userId, $data, $tags)) {
                $_SESSION['success'] = "Profile updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }

            header('Location: /candidate/profile');
            exit();
        }
    }
    
    // protection - verification ila user howa candidate
    private function checkCandidate()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 3) {
            header('Location: /login');
            exit();
        }
    }
}