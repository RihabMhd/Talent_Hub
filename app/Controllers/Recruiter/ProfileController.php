<?php

namespace App\Controllers\Recruiter;

use App\Repository\CompanyRepository;

class ProfileController
{
    private $companyRepository;
    private $twig;

    public function __construct(CompanyRepository $companyRepository, $twig)
    {
        $this->companyRepository = $companyRepository;
        $this->twig = $twig;
    }

    public function show()
    {
        $userId = $_SESSION['user']['id'];
        $company = $this->companyRepository->findByRecruiterId($userId);

        echo $this->twig->render('recruiter/profile.twig', [
            'company' => $company,
            'session' => $_SESSION
        ]);
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    public function update()
    {
        $data = [
            'nom_entreprise' => $_POST['nom_entreprise'],
            'adresse_entreprise' => $_POST['adresse_entreprise'],
            'site_web' => $_POST['site_web'],
            'user_id' => $_SESSION['user']['id']
        ];

        if ($this->companyRepository->updateOrQuery($data)) {
            $_SESSION['success'] = "Company profile updated successfully!";
        }

        header('Location: /recruiter/company');
        exit;
    }
}
