<?php
namespace App\Models;

class Application {
    private $id;
    private $candidate_id;
    private $offer_id;
    private $status;
    private $motivation_message;
    private $cv_path;
    private $created_at;

    // Getters and Setters (Optional, but good practice)
    public function getId() { return $this->id; }
    public function getStatus() { return $this->status; }
    // ... add others as needed
}