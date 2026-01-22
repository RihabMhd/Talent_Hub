<?php

namespace App\Repository;

use App\Config\Database;
use PDO;

class ApplicationRepository
{

    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Get all applications with detailed info (Admin)
     */
    public function findAllWithDetails()
    {
        $sql = "SELECT app.id, 
                       app.status, 
                       app.date_postulation as created_at,
                       app.message_motivation,
                       app.cv_path,
                       -- Candidate Info
                       u.id as candidate_id,
                       u.nom AS candidate_nom, 
                       u.prenom AS candidate_prenom, 
                       u.email AS candidate_email,
                       u.is_active AS user_status,
                       -- Job Info
                       o.id as job_id,
                       o.titre AS job_title,
                       o.salaire as job_salaire,
                       o.lieu as job_lieu,
                       -- Company Info
                       c.nom_entreprise AS company_name
                FROM candidatures app
                JOIN users u ON app.user_id = u.id
                JOIN offres o ON app.offre_id = o.id
                JOIN companies c ON o.company_id = c.id
                ORDER BY app.date_postulation DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find applications by recruiter ID
     */
    public function findByRecruiterId($recruiterId)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom, 
                u.email as candidate_email,
                u.is_active as user_status,
                o.titre as job_title,
                o.id as job_id,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                co.nom_entreprise as company_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                WHERE co.user_id = :recruiter_id
                ORDER BY app.date_postulation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['recruiter_id' => $recruiterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find application by ID
     */
    public function findById($applicationId)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom, 
                u.email as candidate_email,
                u.is_active as user_status,
                o.titre as job_title,
                o.description as job_description,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                o.id as job_id,
                co.nom_entreprise as company_name,
                cat.nom as category_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                INNER JOIN categories cat ON o.category_id = cat.id
                WHERE app.id = :application_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['application_id' => $applicationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find application by ID and recruiter
     */
    public function findByIdAndRecruiter($applicationId, $recruiterId)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom, 
                u.email as candidate_email,
                u.is_active as user_status,
                o.titre as job_title,
                o.description as job_description,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                o.id as job_id,
                co.nom_entreprise as company_name,
                cat.nom as category_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                INNER JOIN categories cat ON o.category_id = cat.id
                WHERE app.id = :application_id 
                AND co.user_id = :recruiter_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'application_id' => $applicationId,
            'recruiter_id' => $recruiterId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if application belongs to recruiter
     */
    public function belongsToRecruiter($applicationId, $recruiterId)
    {
        $sql = "SELECT COUNT(*) as count
                FROM candidatures app
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                WHERE app.id = :application_id 
                AND co.user_id = :recruiter_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'application_id' => $applicationId,
            'recruiter_id' => $recruiterId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Find applications by recruiter and status
     */
    public function findByRecruiterAndStatus($recruiterId, $status)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom, 
                u.email as candidate_email,
                u.is_active as user_status,
                o.titre as job_title,
                o.id as job_id,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                co.nom_entreprise as company_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                WHERE co.user_id = :recruiter_id
                AND app.status = :status
                ORDER BY app.date_postulation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'recruiter_id' => $recruiterId,
            'status' => $status
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search applications by recruiter
     */
    public function searchByRecruiter($recruiterId, $searchTerm)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom, 
                u.email as candidate_email,
                u.is_active as user_status,
                o.titre as job_title,
                o.id as job_id,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                co.nom_entreprise as company_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                WHERE co.user_id = :recruiter_id
                AND (
                    u.nom LIKE :search 
                    OR u.prenom LIKE :search 
                    OR u.email LIKE :search
                    OR o.titre LIKE :search
                )
                ORDER BY app.date_postulation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'recruiter_id' => $recruiterId,
            'search' => '%' . $searchTerm . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get status statistics for a recruiter
     */
    public function getStatusStatsByRecruiter($recruiterId)
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN app.status = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN app.status = 'acceptee' THEN 1 ELSE 0 END) as acceptee,
                SUM(CASE WHEN app.status = 'refusee' THEN 1 ELSE 0 END) as refusee
                FROM candidatures app
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                WHERE co.user_id = :recruiter_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['recruiter_id' => $recruiterId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Statistics: Count applications by status (Admin)
     */
    public function getStatusStats()
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN status = 'acceptee' THEN 1 ELSE 0 END) as acceptee,
                SUM(CASE WHEN status = 'refusee' THEN 1 ELSE 0 END) as refusee
                FROM candidatures";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update application status
     */
    public function updateStatus($applicationId, $status)
    {
        $sql = "UPDATE candidatures 
                SET status = :status 
                WHERE id = :application_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'application_id' => $applicationId
        ]);
    }

    /**
     * Accept a candidature (Update status to 'acceptee')
     */
    public function acceptCandidature(int $id): bool
    {
        return $this->updateStatus($id, 'acceptee');
    }

    /**
     * Reject a candidature (Update status to 'refusee')
     */
    public function rejectCandidature(int $id): bool
    {
        return $this->updateStatus($id, 'refusee');
    }

    /**
     * Delete a candidature
     */
    public function deleteCandidature(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM candidatures WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Block a Candidate (Set is_active = 0)
     */
    public function blockUser($userId)
    {
        $sql = "UPDATE users SET is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Unblock a Candidate (Set is_active = 1)
     */
    public function unblockUser($userId)
    {
        $sql = "UPDATE users SET is_active = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Get applications by user ID (for candidates)
     */
    public function findByUserId($userId)
    {
        $sql = "SELECT app.*, 
                o.titre as job_title,
                o.salaire as job_salaire,
                o.lieu as job_lieu,
                co.nom_entreprise as company_name,
                cat.nom as category_name
                FROM candidatures app
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                INNER JOIN categories cat ON o.category_id = cat.id
                WHERE app.user_id = :user_id
                ORDER BY app.date_postulation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user already applied to an offer
     */
    public function hasApplied($userId, $offreId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM candidatures 
                WHERE user_id = :user_id AND offre_id = :offre_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'offre_id' => $offreId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Create a new application
     */
    public function create($data)
    {
        $sql = "INSERT INTO candidatures (user_id, offre_id, message_motivation, cv_path, status, date_postulation)
                VALUES (:user_id, :offre_id, :message_motivation, :cv_path, 'en_attente', NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'offre_id' => $data['offre_id'],
            'message_motivation' => $data['message_motivation'] ?? null,
            'cv_path' => $data['cv_path']
        ]);
    }

    /**
     * Get application count by offer ID
     */
    public function countByOffre($offreId)
    {
        $sql = "SELECT COUNT(*) as count FROM candidatures WHERE offre_id = :offre_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['offre_id' => $offreId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Get recent applications (for dashboard)
     */
    public function getRecent($limit = 10)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom,
                o.titre as job_title,
                co.nom_entreprise as company_name
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                INNER JOIN offres o ON app.offre_id = o.id
                INNER JOIN companies co ON o.company_id = co.id
                ORDER BY app.date_postulation DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get applications by offer ID
     */
    public function findByOffre($offreId)
    {
        $sql = "SELECT app.*, 
                u.nom as candidate_nom, 
                u.prenom as candidate_prenom,
                u.email as candidate_email,
                u.is_active as user_status
                FROM candidatures app
                INNER JOIN users u ON app.user_id = u.id
                WHERE app.offre_id = :offre_id
                ORDER BY app.date_postulation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['offre_id' => $offreId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}