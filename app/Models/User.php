<?php

namespace App\Model;

class User
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $password;
    private int $role_id;
    private bool $is_active;
    private bool $is_verified;
    private ?string $verified_at;
    private ?string $email_verified_at;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $password = '',
        int $role_id = 0,
        bool $is_active = true,
        bool $is_verified = false,
        ?string $verified_at = null,
        ?string $email_verified_at = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->is_active = $is_active;
        $this->is_verified = $is_verified;
        $this->verified_at = $verified_at;
        $this->email_verified_at = $email_verified_at;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Create User from array
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['role_id'] ?? 0,
            $data['is_active'] ?? true,
            $data['is_verified'] ?? false,
            $data['verified_at'] ?? null,
            $data['email_verified_at'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    // Convert User to array
    public function toArray(bool $includePassword = false): array
    {
        $data = [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'is_active' => $this->is_active,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($includePassword) {
            $data['password'] = $this->password;
        }

        return $data;
    }

    // ----- GETTERS -----
     // ----- SETTERS -----
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
    
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }
    
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
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

    public function isActive(): bool
    {
        return $this->is_active;
    }
    
    public function setActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }
    
    public function setVerified(bool $is_verified): void
    {
        $this->is_verified = $is_verified;
    }

    public function getVerifiedAt(): ?string
    {
        return $this->verified_at;
    }
    
    public function setVerifiedAt(?string $verified_at): void
    {
        $this->verified_at = $verified_at;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->email_verified_at;
    }
    
    public function setEmailVerifiedAt(?string $email_verified_at): void
    {
        $this->email_verified_at = $email_verified_at;
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