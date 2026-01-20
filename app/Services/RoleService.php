<?php
namespace App\Services;
use App\Repository\RoleRepository;
use Exception;

class RoleService
{
    private RoleRepository $roleRepository;
    
    
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    

    public function getAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }
    

    public function getRoleById(int $id): ?array
    {
        return $this->roleRepository->findById($id);
    }
    
 
    public function getRoleByName(string $name): ?array
    {
        return $this->roleRepository->findByName($name);
    }
 
    public function createRole(string $name, string $description): int|false
    {
       
        if ($this->roleRepository->findByName($name)) {
            return false;
        }
        
        try {
            $roleId = $this->roleRepository->create([
                'name' => $name,
                'description' => $description
            ]);
            return $roleId;
        } catch (Exception $e) {
            return false;
        }
    }
    
  
    public function updateRole(int $id, array $data): bool
    {
        
        if (!$this->roleRepository->findById($id)) {
            return false;
        }
        
        if (isset($data['name'])) {
            $existingRole = $this->roleRepository->findByName($data['name']);
            if ($existingRole && $existingRole['id'] !== $id) {
                return false;
            }
        }
        
        return $this->roleRepository->update($id, $data);
    }
    
    public function deleteRole(int $id): bool
    {
        if (!$this->roleRepository->findById($id)) {
            return false;
        }
        
        $users = $this->roleRepository->getUsersByRole($id);
        if (count($users) > 0) {
            return false;
        }
        
        return $this->roleRepository->delete($id);
    }
    
    public function getUsersByRole(int $roleId): array
    {
        return $this->roleRepository->getUsersByRole($roleId);
    }
    
    public function roleExists(string $name): bool
    {
        return $this->roleRepository->findByName($name) !== null;
    }
    
  
    public function getRoleCount(): int
    {
        return count($this->roleRepository->findAll());
    }
}