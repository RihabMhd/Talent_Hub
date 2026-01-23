<?php

namespace App\Repository;

use PDO;

class CompanyRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByRecruiterId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM companies WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrQuery($data) {
        $existing = $this->findByRecruiterId($data['user_id']);
        
        if ($existing) {
            $sql = "UPDATE companies SET 
                    nom_entreprise = :nom_entreprise, 
                    adresse_entreprise = :adresse_entreprise, 
                    site_web = :site_web 
                    WHERE user_id = :user_id";
        } else {
            $sql = "INSERT INTO companies (nom_entreprise, adresse_entreprise, site_web, user_id) 
                    VALUES (:nom_entreprise, :adresse_entreprise, :site_web, :user_id)";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}