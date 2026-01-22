<?php

namespace App\Models;

class Application
{
    private ?int $id = null;
    private int $candidate_id;
    private int $offer_id;
    private string $status;
    private ?string $motivation_message = null;
    private ?string $cv_path = null;
    private ?string $created_at = null;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidateId(): int
    {
        return $this->candidate_id;
    }

    public function getOfferId(): int
    {
        return $this->offer_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMotivationMessage(): ?string
    {
        return $this->motivation_message;
    }

    public function getCvPath(): ?string
    {
        return $this->cv_path;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }


    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCandidateId(int $candidate_id): self
    {
        $this->candidate_id = $candidate_id;
        return $this;
    }

    public function setOfferId(int $offer_id): self
    {
        $this->offer_id = $offer_id;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setMotivationMessage(?string $motivation_message): self
    {
        $this->motivation_message = $motivation_message;
        return $this;
    }

    public function setCvPath(?string $cv_path): self
    {
        $this->cv_path = $cv_path;
        return $this;
    }

    public function setCreatedAt(?string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}
