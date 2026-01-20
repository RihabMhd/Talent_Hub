<?php
namespace App\Repository;

class UserRepository
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    
    public function create(array $userData): ?int
    {
        // Handle both 'name' and 'nom'/'prenom' formats
        $nom = $userData['nom'] ?? $userData['name'] ?? '';
        $prenom = $userData['prenom'] ?? '';
        
        $stmt = $this->db->prepare(
            "INSERT INTO users (nom, prenom, email, password, role_id) 
             VALUES (:nom, :prenom, :email, :password, :role_id)"
        );
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':password', $userData['password']);
        $stmt->bindParam(':role_id', $userData['role_id'], \PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return (int) $this->db->lastInsertId();
        }
        
        return null;
    }
    
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET password = :password WHERE id = :id"
        );
        
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function update(int $id, array $userData): bool
    {
        $nom = $userData['nom'] ?? $userData['name'] ?? '';
        $prenom = $userData['prenom'] ?? '';
        
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET nom = :nom, prenom = :prenom, email = :email, role_id = :role_id 
             WHERE id = :id"
        );
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':role_id', $userData['role_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}