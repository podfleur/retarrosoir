<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_post = null;

    #[ORM\Column]
    private ?int $id_compte = null;

    #[ORM\Column(length: 2200)]
    private ?string $texte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->id_post;
    }

    public function setPostId(int $id_post): static
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }
}
