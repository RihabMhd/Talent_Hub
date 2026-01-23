<?php

namespace App\Models;




class Application
{
    private ?int $id = null;
    private string $nom_entreprise;
    private ?string $adresse_entreprise = null;
    private ?string $site_web = null;


    // ----- GETTERS -----
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): string
    {
        return $this->nom_entreprise;
    }

    public function getAdresseEntreprise(): ?string
    {
        return $this->adresse_entreprise;
    }

    public function getSiteWeb(): ?string
    {
        return $this->site_web;
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

    public function setNomEntreprise(string $nom_entreprise): self
    {
        $this->nom_entreprise = $nom_entreprise;
        return $this;
    }

    public function setAdresseEntreprise(?string $adresse_entreprise): self
    {
        $this->adresse_entreprise = $adresse_entreprise;
        return $this;
    }

    public function setSiteWeb(?string $site_web): self
    {
        $this->site_web = $site_web;
        return $this;
    }
}
