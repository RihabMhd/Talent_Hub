<?php


namespace App\Models;
use PDO;
use App\Config\Database; 

class JobOffer {
    
    private $db;
    private $table = 'offres';

    public function __construct() {
        // 1. Instantiate your Database class
        $database = new Database();
        
        // 2. Get the PDO connection using your method
        $this->db = $database->getConnection();
    }

    
    public function findAllForAdmin($filter = 'all') {
        $sql = "SELECT o.*, 
                       comp.nom_entreprise, 
                       u.nom AS recruteur_nom, 
                       u.prenom AS recruteur_prenom, 
                       cat.nom AS category_name 
                FROM {$this->table} o
                JOIN companies comp ON o.company_id = comp.id
                JOIN users u ON comp.user_id = u.id
                JOIN categories cat ON o.category_id = cat.id";

        // Filter logic
        if ($filter === 'active') {
            $sql .= " WHERE o.deleted_at IS NULL";
        } elseif ($filter === 'archived') {
            $sql .= " WHERE o.deleted_at IS NOT NULL";
        }
        
        $sql .= " ORDER BY o.created_at DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Soft Delete (Archive)
     */
    public function softDelete($id) {
        $sql = "UPDATE {$this->table} 
                SET deleted_at = NOW(), status = 'archived' 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Restore
     */
    public function restore($id) {
        $sql = "UPDATE {$this->table} 
                SET deleted_at = NULL, status = 'active' 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Stats: Count offers per Company
     */
    public function countOffersPerRecruiter() {
        $sql = "SELECT comp.nom_entreprise, COUNT(o.id) as total_offres 
                FROM companies comp
                JOIN offres o ON comp.id = o.company_id
                WHERE o.deleted_at IS NULL
                GROUP BY comp.id, comp.nom_entreprise
                ORDER BY total_offres DESC
                LIMIT 5";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}