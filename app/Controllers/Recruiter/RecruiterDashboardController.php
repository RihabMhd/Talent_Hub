<?php

namespace App\Controllers\Recruiter;

class RecruiterDashboardController
{
    private $twig;
    private $db;

    public function __construct($twig, $db)
    {
        $this->twig = $twig;
        $this->db = $db;
    }

    public function index()
    {
        $userId = $_SESSION['user']['id'] ?? 0;
        
        // n jm3ou toutes les stats و data li ghanst3mloha f dashboard
        $data = [
            'current_user' => $_SESSION['user'] ?? null,
            'total_jobs' => $this->getTotalActiveJobs(),
            'total_application' => $this->getTotalApplication(),
            'pending_applications' => $this->getPendingApplications(),
            'recent_jobs' => $this->getRecentJobs(),
            'company' => $this->getCompanyInfo(),
            'session' => $_SESSION
        ];
        
        echo $this->twig->render('recruiter/dashboard.twig', $data);
        
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }

    // n7sbou ch7al active jobs 3nd recruiter
    private function getTotalActiveJobs(): int
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
            // kan joiniw m3a companies bach njibou ghir jobs dial recruiter had
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM offres o
                INNER JOIN companies c ON o.company_id = c.id
                WHERE c.user_id = :user_id 
                AND o.deleted_at IS NULL
                AND o.status = 'active'
            ");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            error_log("Error getting total jobs: " . $e->getMessage());
            return 0;
        }
    }

    // total candidatures
    private function getTotalApplication(): int
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM candidatures ca
                INNER JOIN offres o ON ca.offre_id = o.id
                INNER JOIN companies c ON o.company_id = c.id
                WHERE c.user_id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            error_log("Error getting total applications: " . $e->getMessage());
            return 0;
        }
    }

    // candidatures li mazal pending - mazal ma acceptاhomch wla refusاhomch
    private function getPendingApplications(): int
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM candidatures ca
                INNER JOIN offres o ON ca.offre_id = o.id
                INNER JOIN companies c ON o.company_id = c.id
                WHERE c.user_id = :user_id
                AND ca.status = 'en_attente'
            ");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            error_log("Error getting pending applications: " . $e->getMessage());
            return 0;
        }
    }

    // derniers 5 jobs li postاhom - m3a count dial applications
    private function getRecentJobs(): array
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT 
                    o.id,
                    o.titre,
                    o.salaire,
                    o.lieu,
                    o.status,
                    o.created_at,
                    cat.nom as category_name,
                    (SELECT COUNT(*) FROM candidatures WHERE offre_id = o.id) as application_count
                FROM offres o
                INNER JOIN companies c ON o.company_id = c.id
                INNER JOIN categories cat ON o.category_id = cat.id
                WHERE c.user_id = :user_id
                AND o.deleted_at IS NULL
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting recent jobs: " . $e->getMessage());
            return [];
        }
    }

    // info dial company - nom, adresse, site web...
    private function getCompanyInfo(): ?array
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT * FROM companies WHERE user_id = :user_id LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log("Error getting company info: " . $e->getMessage());
            return null;
        }
    }
}