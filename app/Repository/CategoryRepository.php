<?php
namespace App\Repository;

use PDO;

class CategoryRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    public function create(array $data): ?int
    {
        $stmt = $this->db->prepare("INSERT INTO categories (nom) VALUES (:nom)");
        $success = $stmt->execute(['nom' => $data['nom']]);
        
        return $success ? (int)$this->db->lastInsertId() : null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET nom = :nom WHERE id = :id");
        return $stmt->execute([
            'nom' => $data['nom'],
            'id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}