<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_photo = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_etablissement = null;

    #[ORM\Column(length: 20)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $nom_affichage = null;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    #[ORM\Column(length: 40)]
    private ?string $mdp = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dernier_golden_like = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPhotoId(): ?int
    {
        return $this->id_photo;
    }

    public function setPhotoId(?int $id_photo): static
    {
        $this->id_photo = $id_photo;

        return $this;
    }

    public function getEtablissementId(): ?int
    {
        return $this->id_etablissement;
    }

    public function setEtablissementId(?int $id_etablissement): static
    {
        $this->id_etablissement = $id_etablissement;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getNomAffichage(): ?string
    {
        return $this->nom_affichage;
    }

    public function setNomAffichage(?string $nom_affichage): static
    {
        $this->nom_affichage = $nom_affichage;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getDernierGoldenLike(): ?\DateTimeInterface
    {
        return $this->dernier_golden_like;
    }

    public function setDernierGoldenLike(?\DateTimeInterface $dernier_golden_like): static
    {
        $this->dernier_golden_like = $dernier_golden_like;

        return $this;
    }
}
