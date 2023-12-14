<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_signaleur = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_post = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_signale = null;

    #[ORM\Column(length: 150)]
    private ?string $motif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdSignaleur(): ?int
    {
        return $this->id_signaleur;
    }

    public function setIdSignaleur(int $id_signaleur): static
    {
        $this->id_signaleur = $id_signaleur;

        return $this;
    }

    public function getIdPost(): ?int
    {
        return $this->id_post;
    }

    public function setIdPost(?int $id_post): static
    {
        $this->id_post = $id_post;

        return $this;
    }

    public function getIdSignale(): ?int
    {
        return $this->id_signale;
    }

    public function setIdSignale(?int $id_signale): static
    {
        $this->id_signale = $id_signale;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }
}
