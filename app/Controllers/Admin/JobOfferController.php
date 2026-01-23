<?php

namespace App\Controllers\Admin;

use App\Repository\JobOfferRepository;
use App\Config\Twig;

class JobOfferController
{
    private $jobOfferRepository;

    public function __construct()
    {
        $this->jobOfferRepository = new JobOfferRepository();
    }

    public function index()
    {
        $this->checkAdmin();

        // n checkew ila 3ndna filter f url (all, active, archived...)
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        
        // njibou offers 7sb filter
        $offers = $this->jobOfferRepository->findAllForAdmin($filter);
        
        // stats - ch7al offre 3nd kola recruiter
        $stats  = $this->jobOfferRepository->countOffersPerRecruiter();

        echo Twig::render('admin/jobs/index.html.twig', [
            'offers' => $offers,
            'stats' => $stats,
            'currentFilter' => $filter,
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'] ?? null,
            'app' => ['request' => ['uri' => $_SERVER['REQUEST_URI'] ?? '']]
        ]);
        
        unset($_SESSION['success'], $_SESSION['error']);
    }

    // archiver offre - soft delete (ma kanmsho7och permanently)
    public function archive($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferRepository->softDelete($id);
            $_SESSION['success'] = 'Job offer archived successfully';
        }
        header('Location: /admin/jobs');
        exit();
    }

    // restaurer offre mn archive
    public function restore($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferRepository->restore($id);
            $_SESSION['success'] = 'Job offer restored successfully';
        }
        header('Location: /admin/jobs');
        exit();
    }

    // afficher form dial modification
    public function edit($id)
    {
        $this->checkAdmin();
        
        $offer = $this->jobOfferRepository->findById($id);
        $categories = $this->jobOfferRepository->getAllCategories();

        // ila ma l9inach offre nrja3 l liste
        if (!$offer) {
            $_SESSION['error'] = 'Job offer not found';
            header('Location: /admin/jobs');
            exit();
        }

        echo Twig::render('admin/jobs/edit.twig', [ 
            'offer' => $offer,
            'categories' => $categories,
            'session' => $_SESSION,
            'current_user' => $_SESSION['user'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    // sauvegarder modifications dial offre
    public function update($id)
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // n jm3ou data mn form
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'salary' => $_POST['salary'],
                'location' => $_POST['location'],
                'category_id' => $_POST['category_id'],
                'status' => $_POST['status']
            ];

            if ($this->jobOfferRepository->update($id, $data)) {
                $_SESSION['success'] = 'Job offer updated successfully';
                header('Location: /admin/jobs');
                exit();
            } else {
                $_SESSION['error'] = 'Failed to update job offer';
                // ila fachel update nrja3 l edit page bach user ichof error
                header("Location: /admin/jobs/$id/edit"); 
                exit();
            }
        }
    }

    // supprimer permanently - hada hard delete
    public function destroy($id)
    {
        $this->checkAdmin();
        if ($id) {
            $this->jobOfferRepository->delete($id);
            $_SESSION['success'] = 'Job offer deleted permanently';
        }
        header('Location: /admin/jobs');
        exit();
    }

    // protection - verification ila user howa admin
    private function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 1) {
            header('Location: /login');
            exit();
        }
    }
}