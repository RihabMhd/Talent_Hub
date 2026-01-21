<?php
namespace App\Repository;

use PDO;

class JobOfferRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
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
                WHERE o.deleted_at IS NULL
                ORDER BY o.created_at ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllWithFilter(string $filter = 'all'): array
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
            $sql .= " WHERE o.deleted_at IS NULL";
        } elseif ($filter === 'archived') {
            $sql .= " WHERE o.deleted_at IS NOT NULL";
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

    public function create(array $data): ?int
    {
        $sql = "INSERT INTO offres 
                (titre, description, company_id, category_id, lieu, type_contrat, salaire, status, created_at) 
                VALUES 
                (:titre, :description, :company_id, :category_id, :lieu, :type_contrat, :salaire, 'active', NOW())";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'company_id' => $data['company_id'],
            'category_id' => $data['category_id'],
            'lieu' => $data['lieu'] ?? null,
            'type_contrat' => $data['type_contrat'] ?? null,
            'salaire' => $data['salaire'] ?? null
        ]);
        
        return $success ? (int)$this->db->lastInsertId() : null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE offres 
                SET titre = :titre, 
                    description = :description, 
                    category_id = :category_id, 
                    lieu = :lieu, 
                    type_contrat = :type_contrat, 
                    salaire = :salaire
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'lieu' => $data['lieu'] ?? null,
            'type_contrat' => $data['type_contrat'] ?? null,
            'salaire' => $data['salaire'] ?? null,
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

    public function getStatsByCompany(): array
    {
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