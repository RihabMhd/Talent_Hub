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

    public function dashboard()
    {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        $stats = $this->appRepo->getCandidateStats($userId);
        $recentApps = $this->appRepo->getRecentApplications($userId);

        $recommendedJobs = [];
        $profile = $this->candidateRepo->findByUserId($userId);

        if (!empty($profile['skills'])) {
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

    public function edit()
    {
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

    public function update()
    {
        $this->checkCandidate();
        $userId = $_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Prepare data to match both 'users' and 'candidates' table requirements
            $data = [
                'nom'              => $_POST['nom'] ?? '',
                'prenom'           => $_POST['prenom'] ?? '',
                'telephone'        => $_POST['telephone'] ?? null,
                'skills'           => $_POST['skills'] ?? null,
                'experience_annee' => $_POST['experience_annee'] ?? 0,
                'expected_salary'  => $_POST['expected_salary'] ?? null, // From form
            ];

            $tags = $_POST['tags'] ?? [];

            // Handle CV upload
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/cvs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $fileName = uniqid() . '_' . basename($_FILES['cv']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                    $data['cv_path'] = $targetPath;
                }
            }

            // Pass $tags as the third argument
            if ($this->candidateRepo->updateProfile($userId, $data, $tags)) {
                $_SESSION['success'] = "Profile updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }

            header('Location: /candidate/profile');
            exit();
        }
    }
    private function checkCandidate()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 3) {
            header('Location: /login');
            exit();
        }
    }
}
