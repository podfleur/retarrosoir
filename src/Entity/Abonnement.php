<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_suiveur = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_suivi_personne = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_suivi_etablissement = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_suivi_hashtag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSuiveur(): ?int
    {
        return $this->id_suiveur;
    }

    public function setIdSuiveur(int $id_suiveur): static
    {
        $this->id_suiveur = $id_suiveur;

        return $this;
    }

    public function getIdSuiviPersonne(): ?int
    {
        return $this->id_suivi_personne;
    }

    public function setIdSuiviPersonne(?int $id_suivi_personne): static
    {
        $this->id_suivi_personne = $id_suivi_personne;

        return $this;
    }

    public function getIdSuiviEtablissement(): ?int
    {
        return $this->id_suivi_etablissement;
    }

    public function setIdSuiviEtablissement(?int $id_suivi_etablissement): static
    {
        $this->id_suivi_etablissement = $id_suivi_etablissement;

        return $this;
    }

    public function getIdSuiviHashtag(): ?int
    {
        return $this->id_suivi_hashtag;
    }

    public function setIdSuiviHashtag(?int $id_suivi_hashtag): static
    {
        $this->id_suivi_hashtag = $id_suivi_hashtag;

        return $this;
    }
}
