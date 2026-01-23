<?php
namespace App\Repository;

use PDO;
use App\Config\Database;

class JobOfferRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

   
    public function findAllForAdmin(string $filter = 'all'): array
    {
        $sql = "SELECT o.*, 
                       comp.nom_entreprise, 
                       u.nom AS recruteur_nom, 
                       u.prenom AS recruteur_prenom, 
                       cat.nom AS category_name 
                FROM offres o
                JOIN companies comp ON o.company_id = comp.id
                JOIN users u ON comp.user_id = u.id
                JOIN categories cat ON o.category_id = cat.id";

        if ($filter === 'active') {
            $sql .= " WHERE o.status = 'active' AND o.deleted_at IS NULL";
        } elseif ($filter === 'archived') {
            $sql .= " WHERE (o.deleted_at IS NOT NULL OR o.status = 'archived')";
        }
        
        $sql .= " ORDER BY o.created_at DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function findById(int $id): ?array
    {
        $sql = "SELECT o.*, 
                       comp.nom_entreprise, 
                       u.nom AS recruteur_nom, 
                       u.prenom AS recruteur_prenom, 
                       cat.nom AS category_name 
                FROM offres o
                JOIN companies comp ON o.company_id = comp.id
                JOIN users u ON comp.user_id = u.id
                JOIN categories cat ON o.category_id = cat.id
                WHERE o.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

   
    public function getAllCategories(): array
    {
        $sql = "SELECT * FROM categories ORDER BY nom ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE offres 
                SET titre = :titre, 
                    description = :description, 
                    category_id = :category_id, 
                    lieu = :lieu, 
                    salaire = :salaire,
                    status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titre' => $data['title'],         
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'lieu' => $data['location'],     
            'salaire' => $data['salary'],     
            'status' => $data['status'],
            'id' => $id
        ]);
    }


    public function softDelete(int $id): bool
    {
        $sql = "UPDATE offres SET deleted_at = NOW(), status = 'archived' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function restore(int $id): bool
    {
        $sql = "UPDATE offres SET deleted_at = NULL, status = 'active' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM offres WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

  
    public function countOffersPerRecruiter(): array
    {
        $sql = "SELECT comp.nom_entreprise, COUNT(o.id) as total_offres 
                FROM companies comp
                JOIN offres o ON comp.id = o.company_id
                WHERE o.deleted_at IS NULL
                GROUP BY comp.id, comp.nom_entreprise
                ORDER BY total_offres DESC
                LIMIT 4";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findFullOffer($id) {
        $sql = "SELECT o.*, 
                       c.nom_entreprise, c.adresse_entreprise, c.site_web, 
                       cat.nom as category_name
                FROM offres o
                LEFT JOIN companies c ON o.company_id = c.id
                LEFT JOIN categories cat ON o.category_id = cat.id
                WHERE o.id = :id"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTagsByOfferId($id) {
        $sql = "SELECT t.* FROM tags t
                JOIN offre_tag ot ON t.id = ot.tag_id
                WHERE ot.offre_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function findAllActive() {
    $sql = "SELECT o.*, 
                   c.nom_entreprise, 
                   cat.nom as category_name 
            FROM offres o
            LEFT JOIN companies c ON o.company_id = c.id
            LEFT JOIN categories cat ON o.category_id = cat.id
            WHERE o.status = 'active'
            ORDER BY o.created_at DESC";
    
    return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

    }
    public function searchActive($keyword) {
        $sql = "SELECT o.*, 
                       c.nom_entreprise, 
                       cat.nom as category_name 
                FROM offres o
                LEFT JOIN companies c ON o.company_id = c.id
                LEFT JOIN categories cat ON o.category_id = cat.id
                WHERE o.status = 'active' 
                AND (
                    o.titre LIKE :keyword 
                    OR o.description LIKE :keyword 
                    OR c.nom_entreprise LIKE :keyword
                )
                ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function findRecommended(array $skills) {
        if (empty($skills)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($skills), '?'));

        $sql = "SELECT DISTINCT o.*, 
                       c.nom_entreprise, 
                       cat.nom as category_name 
                FROM offres o
                JOIN companies c ON o.company_id = c.id
                LEFT JOIN categories cat ON o.category_id = cat.id
                JOIN offre_tag ot ON o.id = ot.offre_id
                JOIN tags t ON ot.tag_id = t.id
                WHERE o.status = 'active'
                AND t.nom IN ($placeholders)
                ORDER BY o.created_at DESC
                LIMIT 6";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($skills);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}   