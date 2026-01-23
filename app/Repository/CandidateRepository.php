<?php

namespace App\Repository;

use App\Config\Database;
use PDO;

class CandidateRepository
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Get candidate profile by User ID (including tags)
     */
    public function findByUserId($userId)
    {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email 
                FROM users u 
                LEFT JOIN candidates c ON u.id = c.user_id 
                WHERE u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        return $profile ?: null;
    }

    /**
     * Update or Create Profile
     */
    /**
     * Update or Create Profile
     */
    public function updateProfile($userId, $data, $tags = [])
    {
        try {
            $this->db->beginTransaction();

            // Update name in Users table
            $stmtUser = $this->db->prepare("UPDATE users SET nom = :nom, prenom = :prenom WHERE id = :id");
            $stmtUser->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'id' => $userId
            ]);

            // Check if candidate exists
            $stmtCheck = $this->db->prepare("SELECT id FROM candidates WHERE user_id = :uid");
            $stmtCheck->execute(['uid' => $userId]);
            $exists = $stmtCheck->fetch();

            if ($exists) {
                $sql = "UPDATE candidates SET 
                    telephone = :telephone,
                    skills = :skills,
                    experience_annee = :experience,
                    expected_salaire = :salary" .
                    (isset($data['cv_path']) ? ", cv_path = :cv_path" : "") .
                    " WHERE user_id = :uid";

                $params = [
                    'telephone' => $data['telephone'],
                    'skills'    => $data['skills'],
                    'experience' => $data['experience_annee'],
                    'salary'    => $data['expected_salary'],
                    'uid'       => $userId
                ];
                if (isset($data['cv_path'])) $params['cv_path'] = $data['cv_path'];

                $this->db->prepare($sql)->execute($params);
            } else {
                // Logic for INSERT if user doesn't have a profile yet...
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function getAllTags()
    {
        return $this->db->query("SELECT * FROM tags ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
