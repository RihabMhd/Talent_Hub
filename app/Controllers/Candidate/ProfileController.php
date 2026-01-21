<?php

namespace App\Controllers;

use App\Repository\CandidateProfileRepository;

class CandidateProfileController
{
    private $candidateRepo;

    public function __construct()
    {
        $this->candidateRepo = new CandidateProfileRepository();
    }

    public function getByUserId($user_id)
    {
        $candidatures = $this->candidateRepo->findByUserId($user_id);
        return $candidatures;
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'user_id'             => $_POST['user_id'],
                'offre_id'            => $_POST['offre_id'],
                'message_motivation' => $_POST['message_motivation'],
                'cv_path'             => $_POST['cv_path'],
                'status'              => $_POST['status'],
                'date_postulation'   => date('Y-m-d'),
            ];

            if ($this->candidateRepo->create($data)) {
                echo "Candidature ajoutée avec succès";
            } else {
                echo "Erreur lors de l'ajout";
            }
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'user_id'             => $_POST['user_id'],
                'offre_id'            => $_POST['offre_id'],
                'message_motivation' => $_POST['message_motivation'],
                'cv_path'             => $_POST['cv_path'],
                'status'              => $_POST['status'],
                'date_postulation'   => $_POST['date_postulation'],
            ];

            if ($this->candidateRepo->update($id, $data)) {
                echo "Candidature mise à jour";
            } else {
                echo "Erreur lors de la mise à jour";
            }
        }
    }

    public function delete($id)
    {
        if ($this->candidateRepo->delete($id)) {
            echo "Candidature supprimée";
        } else {
            echo "Erreur lors de la suppression";
        }
    }
}
