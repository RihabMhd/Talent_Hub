<?php
namespace App\Repository;

use App\Config\Database;
use PDO;

class ApplicationRepository {
    
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Get all applications with detailed info
     */
    public function findAllWithDetails() {
        $sql = "SELECT app.id, 
                       app.status, 
                       app.date_postulation as created_at,
                       -- Candidate Info
                       u.id as candidate_id,
                       u.nom AS candidate_nom, 
                       u.prenom AS candidate_prenom, 
                       u.email AS candidate_email,
                       u.is_active AS user_status, -- 1=Active, 0=Banned
                       -- Job Info
                       o.titre AS job_title, 
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
     * Block a Candidate (Set is_active = 0)
     */
    public function blockUser($userId) {
        $sql = "UPDATE users SET is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Unblock a Candidate (Set is_active = 1)
     */
    public function unblockUser($userId) {
        $sql = "UPDATE users SET is_active = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Statistics: Count applications by status
     */
    public function getStatusStats() {
        $sql = "SELECT status, COUNT(*) as count FROM candidatures GROUP BY status";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Accept a candidature (Update status to 'acceptee')
     */
    public function acceptCandidature(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE candidatures 
             SET status = 'acceptee'
             WHERE id = :id"
        );
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Reject a candidature (Update status to 'refusee')
     */
    public function rejectCandidature(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE candidatures 
             SET status = 'refusee'
             WHERE id = :id"
        );
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function deleteCandidature(int $id): bool
{
    $stmt = $this->db->prepare("DELETE FROM candidatures WHERE id = :id");
    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    return $stmt->execute();
}
}