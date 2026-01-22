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

    /**
     * Display all applications for recruiter's job offers
     */
    public function index()
    {
        $this->checkRecruiter();

        $recruiterId = $_SESSION['user']['id'];

        // Get all applications for this recruiter's job offers
        $applications = $this->applicationRepository->findByRecruiterId($recruiterId);
        $stats = $this->applicationRepository->getStatusStatsByRecruiter($recruiterId);

        echo Twig::render('recruiter/applications/index.twig', [
            'applications' => $applications,
            'stats' => $stats,
            'session' => $_SESSION,
            'uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    /**
     * View candidate profile and application details
     */
    public function viewCandidate($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Application not found';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];
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

    /**
     * Accept a candidate's application
     */
    public function accept($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Invalid application';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];

        // Validate application belongs to recruiter
        if (!$this->applicationRepository->belongsToRecruiter($applicationId, $recruiterId)) {
            $_SESSION['error'] = 'Unauthorized action';
            header('Location: /recruiter/applications');
            exit();
        }

        $result = $this->applicationRepository->updateStatus($applicationId, 'acceptee');

        if ($result) {
            $_SESSION['success'] = 'Candidature acceptée avec succès';
        } else {
            $_SESSION['error'] = 'Échec de l\'acceptation de la candidature';
        }

        header('Location: /recruiter/applications');
        exit();
    }

    /**
     * Reject a candidate's application
     */
    public function reject($applicationId)
    {
        $this->checkRecruiter();

        if (!$applicationId) {
            $_SESSION['error'] = 'Invalid application';
            header('Location: /recruiter/applications');
            exit();
        }

        $recruiterId = $_SESSION['user']['id'];

        // Validate application belongs to recruiter
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

    /**
     * Filter applications by status
     */
    public function filterByStatus()
    {
        $this->checkRecruiter();

        $status = $_GET['status'] ?? 'all';
        $recruiterId = $_SESSION['user']['id'];

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

    /**
     * Search applications by candidate name or email
     */
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

    /**
     * Check if user is a recruiter
     */
    private function checkRecruiter()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] !== 2) {
            header('Location: /login');
            exit();
        }
    }
}
