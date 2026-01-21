<?php

namespace App\Controllers;

use App\Repository\CandidateProfileRepository;



class CandidateController
{
    private CandidateRepository $candidateRepository;

    public function __construct(PDO $db)
    {
        $this->candidateRepository = new CandidateRepository($db);
        session_start();
    }

    
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $fullName = trim($_POST['full_name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($fullName) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Tous les champs sont obligatoires';
                header('Location: /register');
                exit;
            }

            if ($this->candidateRepository->findByEmail($email)) {
                $_SESSION['error'] = 'Email déjà utilisé';
                header('Location: /register');
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $candidate = new Candidate(
                fullName: $fullName,
                email: $email,
                password: $hashedPassword
            );

            $this->candidateRepository->create($candidate);

            $_SESSION['success'] = 'Compte créé avec succès';
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../views/candidate/register.php';
    }

    
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $candidate = $this->candidateRepository->verifyLogin($email, $password);

            if (!$candidate) {
                $_SESSION['error'] = 'Identifiants invalides';
                header('Location: /login');
                exit;
            }

            $_SESSION['user'] = [
                'id'    => $candidate->getId(),
                'name'  => $candidate->getFullName(),
                'email' => $candidate->getEmail(),
                'role'  => $candidate->getRole()
            ];

            header('Location: /candidate/dashboard');
            exit;
        }

        require __DIR__ . '/../views/candidate/login.php';
    }

    
    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

   
    public function dashboard(): void
    {
        $this->checkAuth();

        require __DIR__ . '/../views/candidate/dashboard.php';
    }

   
    public function updateProfile(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $candidate = $this->candidateRepository
                              ->findById($_SESSION['user']['id']);

            if (!$candidate) {
                header('HTTP/1.1 403 Forbidden');
                exit('Accès interdit');
            }

            $candidate->setFullName($_POST['full_name'] ?? $candidate->getFullName());
            $candidate->setPhone($_POST['phone'] ?? null);
            $candidate->setSkills($_POST['skills'] ?? null);
            $candidate->setExperienceYears((int)($_POST['experience_years'] ?? 0));
            $candidate->setExpectedSalary(
                $_POST['expected_salary'] !== '' ? (float)$_POST['expected_salary'] : null
            );

          
            if (!empty($_FILES['cv']['name'])) {
                $cvPath = $this->uploadCV($_FILES['cv']);
                $candidate->setCvPath($cvPath);
            }

            $this->candidateRepository->update($candidate);

            $_SESSION['success'] = 'Profil mis à jour';
            header('Location: /candidate/dashboard');
            exit;
        }
    }

  
    private function checkAuth(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'candidat') {
            header('HTTP/1.1 403 Forbidden');
            exit('Accès refusé');
        }
    }

    private function uploadCV(array $file): string
    {
        $allowedTypes = ['application/pdf'];
        $maxSize = 2 * 1024 * 1024; 

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('Fichier trop volumineux');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/cv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid('cv_') . '.pdf';
        move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);

        return '/uploads/cv/' . $fileName;
    }
}
