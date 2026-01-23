<?php

namespace App\Models;

use PDO;
use App\Config\Database; 

class JobOffer {
    
    private $db;
    private $table = 'offres';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        // Injection simple de PDO connection, katdir connexion wa7da f class kamla
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
        // JOINs: bach admin ychof offer + company + recruiter + category f query wa7da

        if ($filter === 'active') {
            $sql .= " WHERE o.deleted_at IS NULL";
            // Active = soft delete ma darhach
        } elseif ($filter === 'archived') {
            $sql .= " WHERE o.deleted_at IS NOT NULL";
            // Archived = soft deleted
        }
        
        $sql .= " ORDER BY o.created_at DESC";
        // Order par date, latest offers f louwel

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        // query() direct ok hna 7it ma kaynach user input (safe)
    }

    public function findById($id) {
        $sql = "SELECT o.*, 
                       comp.nom_entreprise, 
                       u.nom AS recruteur_nom, 
                       u.prenom AS recruteur_prenom, 
                       cat.nom AS category_name 
                FROM {$this->table} o
                JOIN companies comp ON o.company_id = comp.id
                JOIN users u ON comp.user_id = u.id
                JOIN categories cat ON o.category_id = cat.id
                WHERE o.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        // Prepared statement important hna bach n7miw mn SQL Injection

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (titre, description, company_id, category_id, lieu, type_contrat, salaire, status, created_at) 
                VALUES 
                (:titre, :description, :company_id, :category_id, :lieu, :type_contrat, :salaire, 'active', NOW())";
        // status auto = active, created_at auto = NOW()
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'company_id' => $data['company_id'],
            'category_id' => $data['category_id'],
            'lieu' => $data['lieu'] ?? null,
            'type_contrat' => $data['type_contrat'] ?? null,
            'salaire' => $data['salaire'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET titre = :titre, 
                    description = :description, 
                    company_id = :company_id, 
                    category_id = :category_id, 
                    lieu = :lieu, 
                    type_contrat = :type_contrat, 
                    salaire = :salaire
                WHERE id = :id";
        // Update classique, ma katbdlch status wla deleted_at
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'company_id' => $data['company_id'],
            'category_id' => $data['category_id'],
            'lieu' => $data['lieu'] ?? null,
            'type_contrat' => $data['type_contrat'] ?? null,
            'salaire' => $data['salaire'] ?? null,
            'id' => $id
        ]);
    }

    public function softDelete($id) {
        $sql = "UPDATE {$this->table} 
                SET deleted_at = NOW(), status = 'archived' 
                WHERE id = :id";
        // Soft delete: ma kan7ydofch record, ghir kanarchiveh
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function restore($id) {
        $sql = "UPDATE {$this->table} 
                SET deleted_at = NULL, status = 'active' 
                WHERE id = :id";
        // Restore offer li kan archived
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function countOffersPerRecruiter() {
        $sql = "SELECT comp.nom_entreprise, COUNT(o.id) as total_offres 
                FROM companies comp
                JOIN offres o ON comp.id = o.company_id
                WHERE o.deleted_at IS NULL
                GROUP BY comp.id, comp.nom_entreprise
                ORDER BY total_offres DESC
                LIMIT 5";
        // Stats: top 5 companies li 3ndhom akthar offres active
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
