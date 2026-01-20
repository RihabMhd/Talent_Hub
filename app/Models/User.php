<?php
namespace App\Model;
class User
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;
    private int $role_id;
    private ?string $created_at;
    private ?string $updated_at;
    
    public function __construct(
        ?int $id = null,
        string $name = '',
        string $email = '',
        string $password = '',
        int $role_id = 0,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['role_id'] ?? 0,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }
    
    
    public function toArray(bool $includePassword = false): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id
        ];
        
        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        
        if ($includePassword) {
            $data['password'] = $this->password;
        }
        
        if ($this->created_at !== null) {
            $data['created_at'] = $this->created_at;
        }
        
        if ($this->updated_at !== null) {
            $data['updated_at'] = $this->updated_at;
        }
        
        return $data;
    }
    
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    
    public function getName(): string
    {
        return $this->name;
    }
    
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
   
    public function getPassword(): string
    {
        return $this->password;
    }
    
 
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    
    
    public function getRoleId(): int
    {
        return $this->role_id;
    }
    
    
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }
    

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
   
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
    
   
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
    
  
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}