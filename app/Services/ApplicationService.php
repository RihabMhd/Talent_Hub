<?php
namespace App\Services;

use App\Repository\ApplicationRepository;

class ApplicationService {
    
    private $applicationRepository;

    public function __construct() {
        $this->applicationRepository = new ApplicationRepository();
    }

    /**
     * Get all applications with full details
     * 
     * @return array List of all applications with candidate, job, and company info
     */
    public function getAllApplications(): array {
        try {
            return $this->applicationRepository->findAllWithDetails();
        } catch (\Exception $e) {
            error_log("Error fetching applications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get applications grouped by status
     * 
     * @return array Applications organized by status (en_attente, acceptee, refusee)
     */
    public function getApplicationsByStatus(): array {
        $applications = $this->getAllApplications();
        
        $grouped = [
            'en_attente' => [],
            'acceptee' => [],
            'refusee' => []
        ];

        foreach ($applications as $app) {
            $status = $app['status'] ?? 'en_attente';
            if (isset($grouped[$status])) {
                $grouped[$status][] = $app;
            }
        }

        return $grouped;
    }

    /**
     * Accept an application
     * 
     * @param int $applicationId
     * @return bool Success status
     */
    public function acceptApplication(int $applicationId): bool {
        try {
            $result = $this->applicationRepository->acceptCandidature($applicationId);
            
            if ($result) {
                // You could add email notification here
                // $this->sendAcceptanceEmail($applicationId);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error accepting application {$applicationId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject an application
     * 
     * @param int $applicationId
     * @return bool Success status
     */
    public function rejectApplication(int $applicationId): bool {
        try {
            $result = $this->applicationRepository->rejectCandidature($applicationId);
            
            if ($result) {
                // You could add email notification here
                // $this->sendRejectionEmail($applicationId);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error rejecting application {$applicationId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Block a candidate (prevent them from applying)
     * 
     * @param int $userId Candidate's user ID
     * @return bool Success status
     */
    public function blockCandidate(int $userId): bool {
        try {
            return $this->applicationRepository->blockUser($userId);
        } catch (\Exception $e) {
            error_log("Error blocking user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unblock a candidate
     * 
     * @param int $userId Candidate's user ID
     * @return bool Success status
     */
    public function unblockCandidate(int $userId): bool {
        try {
            return $this->applicationRepository->unblockUser($userId);
        } catch (\Exception $e) {
            error_log("Error unblocking user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get application statistics
     * 
     * @return array Statistics including counts by status and total
     */
    public function getStatistics(): array {
        try {
            $statusStats = $this->applicationRepository->getStatusStats();
            
            $stats = [
                'total' => 0,
                'en_attente' => 0,
                'acceptee' => 0,
                'refusee' => 0,
                'acceptance_rate' => 0
            ];

            foreach ($statusStats as $stat) {
                $status = $stat['status'];
                $count = (int) $stat['count'];
                
                $stats['total'] += $count;
                
                if (isset($stats[$status])) {
                    $stats[$status] = $count;
                }
            }

            // Calculate acceptance rate
            if ($stats['total'] > 0) {
                $stats['acceptance_rate'] = round(
                    ($stats['acceptee'] / $stats['total']) * 100, 
                    2
                );
            }

            return $stats;
        } catch (\Exception $e) {
            error_log("Error fetching statistics: " . $e->getMessage());
            return [
                'total' => 0,
                'en_attente' => 0,
                'acceptee' => 0,
                'refusee' => 0,
                'acceptance_rate' => 0
            ];
        }
    }

    /**
     * Get applications for a specific candidate
     * 
     * @param int $userId Candidate's user ID
     * @return array List of applications for the candidate
     */
    public function getApplicationsByCandidate(int $userId): array {
        $allApplications = $this->getAllApplications();
        
        return array_filter($allApplications, function($app) use ($userId) {
            return $app['candidate_id'] == $userId;
        });
    }

    /**
     * Check if a candidate is blocked
     * 
     * @param int $userId Candidate's user ID
     * @return bool True if blocked, false otherwise
     */
    public function isCandidateBlocked(int $userId): bool {
        $applications = $this->getApplicationsByCandidate($userId);
        
        if (!empty($applications)) {
            $firstApp = reset($applications);
            return $firstApp['user_status'] == 0;
        }
        
        return false;
    }

    /**
     * Validate application data before processing
     * 
     * @param int $applicationId
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateApplication(int $applicationId): array {
        $applications = $this->getAllApplications();
        
        $application = null;
        foreach ($applications as $app) {
            if ($app['id'] == $applicationId) {
                $application = $app;
                break;
            }
        }

        if (!$application) {
            return [
                'valid' => false,
                'message' => 'Application not found'
            ];
        }

        if ($application['user_status'] == 0) {
            return [
                'valid' => false,
                'message' => 'Cannot process application - candidate is blocked'
            ];
        }

        if ($application['status'] !== 'en_attente') {
            return [
                'valid' => false,
                'message' => 'Application has already been processed'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Application is valid',
            'data' => $application
        ];
    }

    /**
     * Bulk accept applications
     * 
     * @param array $applicationIds Array of application IDs
     * @return array ['success' => int, 'failed' => int]
     */
    public function bulkAcceptApplications(array $applicationIds): array {
        $success = 0;
        $failed = 0;

        foreach ($applicationIds as $id) {
            if ($this->acceptApplication($id)) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed
        ];
    }

    /**
     * Bulk reject applications
     * 
     * @param array $applicationIds Array of application IDs
     * @return array ['success' => int, 'failed' => int]
     */
    public function bulkRejectApplications(array $applicationIds): array {
        $success = 0;
        $failed = 0;

        foreach ($applicationIds as $id) {
            if ($this->rejectApplication($id)) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed
        ];
    }
}