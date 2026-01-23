<?php

namespace App\Controllers\Recruiter;

use App\Repository\ApplicationRepository;
use App\Config\Twig;

class ApplicationController
{
    private $applicationRepository;

    public function __construct()
    {
        $this->applicationRepository = new ApplicationRepository();
    }

    // afficher toutes les candidatures dial job offers dyal recruiter
    public function index()
    {
        $this->checkRecruiter();

        $recruiterId = $_SESSION['user']['id'];

        // njibou candidatures kamlin li jiw 3la offers dyalo
        $applications = $this->applicationRepository->findByRecruiterId($recruiterId);
        
        // stats dial status - ch7al pending, accepté, refusé
        $stats = $this->applicationRepository->getStatusStatsByRecruiter($recruiterId);

        echo Twig::render('recruiter/applications/index.twig', [
            'applications' => $applications,
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    // afficher détails dial candidature w profile candidate
    public function viewCandidate($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Application not found';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];
        
        // n checkew ila had application t3lq b jobs dial recruiter
        // 7it ma y9derch ychof candidatures dial recruiter akhor
        $application = $this->applicationRepository->findByIdAndRecruiter($applicationId, $recruiterId);

        if (!$application) {
            $_SESSION['error'] = 'Application not found or access denied';
            header('Location: /recruiter/applications');
            exit();
        }

        echo Twig::render('recruiter/applications/view_candidate.twig', [
            'application' => $application,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    // accepter candidature
    public function accept($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Invalid application';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];

        // double check ila application t3lq b recruiter had
        if (!$this->applicationRepository->belongsToRecruiter($applicationId, $recruiterId)) {
            $_SESSION['error'] = 'Unauthorized action';
            header('Location: /recruiter/applications');
            exit();
        }

        // nbdlo status l acceptée
        $result = $this->applicationRepository->updateStatus($applicationId, 'acceptee');

        if ($result) {
            $_SESSION['success'] = 'Candidature acceptée avec succès';
        } else {
            $_SESSION['error'] = 'Échec de l\'acceptation de la candidature';
        }

        header('Location: /recruiter/applications');
        exit();
    }

    // refuser candidature
    public function reject($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Invalid application';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];

        // verification ownership
        if (!$this->applicationRepository->belongsToRecruiter($applicationId, $recruiterId)) {
            $_SESSION['error'] = 'Unauthorized action';
            header('Location: /recruiter/applications');
            exit();
        }

        $result = $this->applicationRepository->updateStatus($applicationId, 'refusee');

        if ($result) {
            $_SESSION['success'] = 'Candidature refusée avec succès';
        } else {
            $_SESSION['error'] = 'Échec du refus de la candidature';
        }

        header('Location: /recruiter/applications');
        exit();
    }

    // filtrer candidatures 7sb status
    public function filterByStatus()
    {
        $this->checkRecruiter();

        $status = $_GET['status'] ?? 'all';
        $recruiterId = $_SESSION['user']['id'];

        // ila status machi 'all', n filteriw
        if ($status !== 'all') {
            $applications = $this->applicationRepository->findByRecruiterAndStatus($recruiterId, $status);
        } else {
            $applications = $this->applicationRepository->findByRecruiterId($recruiterId);
        }

        $stats = $this->applicationRepository->getStatusStatsByRecruiter($recruiterId);

        echo Twig::render('recruiter/applications/index.twig', [
            'applications' => $applications,
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI'],
            'filter_status' => $status
        ]);
    }

    // search f candidatures - b name wla email dial candidate
    public function search()
    {
        $this->checkRecruiter();

        $searchTerm = $_GET['q'] ?? '';
        $recruiterId = $_SESSION['user']['id'];

        $applications = $this->applicationRepository->searchByRecruiter($recruiterId, $searchTerm);
        $stats = $this->applicationRepository->getStatusStatsByRecruiter($recruiterId);

        echo Twig::render('recruiter/applications/index.twig', [
            'applications' => $applications,
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI'],
            'search_term' => $searchTerm
        ]);
    }

    // protection - ghir recruiter (role_id = 2)
    private function checkRecruiter()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 2) {
            header('Location: /login');
            exit();
        }
    }
}