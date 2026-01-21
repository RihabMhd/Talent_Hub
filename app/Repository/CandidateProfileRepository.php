<?php 

namespace App\Repository ;

use App\Models\CandidateProfile ;
use App\Config\Database ;

class CandidateRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(Candidate $candidate): bool
    {
        $sql = "INSERT INTO candidates 
                (full_name, email, password, phone, skills, experience_years, expected_salary, cv_path, role, is_active)
                VALUES 
                (:full_name, :email, :password, :phone, :skills, :experience_years, :expected_salary, :cv_path, :role, :is_active)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':full_name'        => $candidate->getFullName(),
            ':email'            => $candidate->getEmail(),
            ':password'         => $candidate->getPassword(),
            ':phone'            => $candidate->getPhone(),
            ':skills'           => $candidate->getSkills(),
            ':experience_years' => $candidate->getExperienceYears(),
            ':expected_salary'  => $candidate->getExpectedSalary(),
            ':cv_path'          => $candidate->getCvPath(),
            ':role'             => $candidate->getRole(),
            ':is_active'        => $candidate->isActive()
        ]);
    }

    

    public function findById(int $id): ?Candidate
    {
        $sql = "SELECT * FROM candidates WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToCandidate($data) : null;
    }

    public function findByEmail(string $email): ?Candidate
    {
        $sql = "SELECT * FROM candidates WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToCandidate($data) : null;
    }

    public function findAllActive(): array
    {
        $sql = "SELECT * FROM candidates WHERE is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $candidates = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $candidates[] = $this->mapToCandidate($row);
        }

        return $candidates;
    }

    

    public function update(Candidate $candidate): bool
    {
        $sql = "UPDATE candidates SET
                    full_name = :full_name,
                    phone = :phone,
                    skills = :skills,
                    experience_years = :experience_years,
                    expected_salary = :expected_salary,
                    cv_path = :cv_path,
                    is_active = :is_active
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':full_name'        => $candidate->getFullName(),
            ':phone'            => $candidate->getPhone(),
            ':skills'           => $candidate->getSkills(),
            ':experience_years' => $candidate->getExperienceYears(),
            ':expected_salary'  => $candidate->getExpectedSalary(),
            ':cv_path'          => $candidate->getCvPath(),
            ':is_active'        => $candidate->isActive(),
            ':id'               => $candidate->getId()
        ]);
    }

   

    public function verifyLogin(string $email, string $password): ?Candidate
    {
        $candidate = $this->findByEmail($email);

        if ($candidate && password_verify($password, $candidate->getPassword())) {
            return $candidate;
        }

        return null;
    }



    public function deactivate(int $id): bool
    {
        $sql = "UPDATE candidates SET is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

  

    private function mapToCandidate(array $data): Candidate
    {
        return new Candidate(
            id: (int)$data['id'],
            fullName: $data['full_name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone'],
            skills: $data['skills'],
            experienceYears: (int)$data['experience_years'],
            expectedSalary: $data['expected_salary'] !== null ? (float)$data['expected_salary'] : null,
            cvPath: $data['cv_path'],
            role: $data['role'],
            isActive: (bool)$data['is_active'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at']
        );
    }
}