<?php 

namespace App\Repository ;

use App\Models\CandidateProfile ;
use App\Config\Database ;

class CandidateProfileRepository {

  private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

  public function findByUserId($user_id)
{
    $sql = "SELECT * FROM candidatures WHERE user_id = :user_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = new \App\Models\CandidateProfile($row);
    }

    return $results; 
}


public function create($data)
{
    $stmt = $this->conn->prepare("
        INSERT INTO candidatures 
        (user_id, offre_id, message_motivation, cv_path, status, date_postulation)
        VALUES 
        (:user_id, :offre_id, :message_motivation, :cv_path, :status, :date_postulation)
    ");

    return $stmt->execute([
        'user_id'             => $data['user_id'],
        'offre_id'            => $data['offre_id'],
        'message_motivation' => $data['message_motivation'],
        'cv_path'             => $data['cv_path'],
        'status'              => $data['status'],
        'date_postulation'   => $data['date_postulation'],
    ]);
}

public function update($id, $data)
{
    $stmt = $this->conn->prepare("
        UPDATE candidatures 
        SET user_id = :user_id,
            offre_id = :offre_id,
            message_motivation = :message_motivation,
            cv_path = :cv_path,
            status = :status,
            date_postulation = :date_postulation
        WHERE id = :id
    ");

    return $stmt->execute([
        'user_id'             => $data['user_id'],
        'offre_id'            => $data['offre_id'],
        'message_motivation' => $data['message_motivation'],
        'cv_path'             => $data['cv_path'],
        'status'              => $data['status'],
        'date_postulation'   => $data['date_postulation'],
        'id'                 => $id
    ]);
}


public function delete($id)
{
    $stmt = $this->conn->prepare("
        DELETE FROM candidatures 
        WHERE id = :id
    ");

    return $stmt->execute([
        'id' => $id
    ]);
}



}