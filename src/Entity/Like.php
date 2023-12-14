<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_post = null;

    #[ORM\Column]
    private ?int $id_compte = null;

    #[ORM\Column(nullable: true)]
    private ?bool $golden = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdPost(): ?int
    {
        return $this->id_post;
    }

    public function setIdPost(int $id_post): static
    {
        $this->id_post = $id_post;

        return $this;
    }

    public function getIdCompte(): ?int
    {
        return $this->id_compte;
    }

    public function setIdCompte(int $id_compte): static
    {
        $this->id_compte = $id_compte;

        return $this;
    }

    public function isGolden(): ?bool
    {
        return $this->golden;
    }

    public function setGolden(?bool $golden): static
    {
        $this->golden = $golden;

        return $this;
    }
}
