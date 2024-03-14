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

    #[ORM\Column(length: 150)]
    private ?string $motif = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compte $signaleur_id = null;

    #[ORM\ManyToOne]
    private ?Compte $signale_id = null;

    #[ORM\ManyToOne]
    private ?Post $post_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getSignaleurId(): ?Compte
    {
        return $this->signaleur_id;
    }

    public function setSignaleurId(?Compte $signaleur_id): static
    {
        $this->signaleur_id = $signaleur_id;

        return $this;
    }

    public function getSignaleId(): ?Compte
    {
        return $this->signale_id;
    }

    public function setSignaleId(?Compte $signale_id): static
    {
        $this->signale_id = $signale_id;

        return $this;
    }

    public function getPostId(): ?Post
    {
        return $this->post_id;
    }

    public function setPostId(?Post $post_id): static
    {
        $this->post_id = $post_id;

        return $this;
    }
}
