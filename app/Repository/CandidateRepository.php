<?php
namespace App\Repository;

use App\Config\Database;
use PDO;

class CandidateRepository {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Get candidate profile by User ID (including tags)
     */
    public function findByUserId($userId) {
        // 1. Get Profile Data
        $sql = "SELECT c.*, u.nom, u.prenom, u.email 
                FROM users u 
                LEFT JOIN candidates c ON u.id = c.user_id 
                WHERE u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) return null;

        // 2. Get Associated Tags
        $profile['tags'] = [];
        if (!empty($profile['id'])) { // If candidate record exists
            $sqlTags = "SELECT t.id, t.nom 
                        FROM tags t 
                        JOIN candidate_tag ct ON t.id = ct.tag_id 
                        WHERE ct.candidate_id = :cid";
            $stmtTags = $this->db->prepare($sqlTags);
            $stmtTags->execute(['cid' => $profile['id']]);
            $profile['tags'] = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
        }

        return $profile;
    }

    /**
     * Update or Create Profile
     */
    public function updateProfile($userId, $data, $tags = []) {
        try {
            $this->db->beginTransaction();

            // 1. Update Basic User Info (Name)
            $stmtUser = $this->db->prepare("UPDATE users SET nom = :nom, prenom = :prenom WHERE id = :id");
            $stmtUser->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'id' => $userId
            ]);

            // 2. Check if candidate record exists
            $stmtCheck = $this->db->prepare("SELECT id FROM candidates WHERE user_id = :uid");
            $stmtCheck->execute(['uid' => $userId]);
            $candidate = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($candidate) {
                // Update existing
                $candidateId = $candidate['id'];
                $sql = "UPDATE candidates SET 
                        titre = :titre, telephone = :tel, adresse = :addr, 
                        salaire_min = :sal, disponibilite = :dispo, experience = :exp 
                        WHERE id = :id";
                $params = [
                    'titre' => $data['titre'], 'tel' => $data['telephone'], 
                    'addr' => $data['adresse'], 'sal' => $data['salaire_min'], 
                    'dispo' => $data['disponibilite'], 'exp' => $data['experience'],
                    'id' => $candidateId
                ];
                $this->db->prepare($sql)->execute($params);
            } else {
                // Create new
                $sql = "INSERT INTO candidates (user_id, titre, telephone, adresse, salaire_min, disponibilite, experience) 
                        VALUES (:uid, :titre, :tel, :addr, :sal, :dispo, :exp)";
                $params = [
                    'uid' => $userId,
                    'titre' => $data['titre'], 'tel' => $data['telephone'], 
                    'addr' => $data['adresse'], 'sal' => $data['salaire_min'], 
                    'dispo' => $data['disponibilite'], 'exp' => $data['experience']
                ];
                $this->db->prepare($sql)->execute($params);
                $candidateId = $this->db->lastInsertId();
            }

            // 3. Sync Tags (Delete old, Insert new)
            $this->db->prepare("DELETE FROM candidate_tag WHERE candidate_id = :cid")->execute(['cid' => $candidateId]);
            
            if (!empty($tags)) {
                $sqlTag = "INSERT INTO candidate_tag (candidate_id, tag_id) VALUES (:cid, :tid)";
                $stmtTag = $this->db->prepare($sqlTag);
                foreach ($tags as $tagId) {
                    $stmtTag->execute(['cid' => $candidateId, 'tid' => $tagId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAllTags() {
        return $this->db->query("SELECT * FROM tags ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}