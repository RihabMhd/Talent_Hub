<?php 

namespace App\Models ;



class Candidate
{
    private ?int $id;
    private string $fullName;
    private string $email;
    private string $password;
    private ?string $phone;
    private ?string $skills;
    private int $experienceYears;
    private ?float $expectedSalary;
    private ?string $cvPath;
    private string $role;
    private bool $isActive;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $fullName = '',
        string $email = '',
        string $password = '',
        ?string $phone = null,
        ?string $skills = null,
        int $experienceYears = 0,
        ?float $expectedSalary = null,
        ?string $cvPath = null,
        string $role = 'candidat',
        bool $isActive = true,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->skills = $skills;
        $this->experienceYears = $experienceYears;
        $this->expectedSalary = $expectedSalary;
        $this->cvPath = $cvPath;
        $this->role = $role;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function getExperienceYears(): int
    {
        return $this->experienceYears;
    }

    public function getExpectedSalary(): ?float
    {
        return $this->expectedSalary;
    }

    public function getCvPath(): ?string
    {
        return $this->cvPath;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

   

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function setSkills(?string $skills): void
    {
        $this->skills = $skills;
    }

    public function setExperienceYears(int $years): void
    {
        $this->experienceYears = $years;
    }

    public function setExpectedSalary(?float $salary): void
    {
        $this->expectedSalary = $salary;
    }

    public function setCvPath(?string $cvPath): void
    {
        $this->cvPath = $cvPath;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
