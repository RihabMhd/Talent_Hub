<?php
namespace App\Repository;
use App\Repository\BaseRepository;
use PDO;
class RoleRepository extends BaseRepository
{
    
    public function getTableName(): string
    {
        return 'roles';
    }
    

    public function findByName(string $name): ?array
    {
        $sql = "SELECT * FROM roles WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    

    public function getUsersByRole(int $role_id): array
    {
        $sql = "SELECT u.* FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE r.id = :role_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role_id' => $role_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}