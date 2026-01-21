<?php

namespace App\Controllers\Recruiter;

class DashboardController
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

    private function getTotalActiveJobs(): int
    {
        try {
            $userId = $_SESSION['user']['id'] ?? 0;
            
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