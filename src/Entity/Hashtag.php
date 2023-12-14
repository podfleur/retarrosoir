<?php

namespace App\Entity;

use App\Repository\HashtagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HashtagRepository::class)]
class Hashtag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_post = null;

    #[ORM\Column(length: 25)]
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

    public function getIdPost(): ?int
    {
        return $this->id_post;
    }

    public function setIdPost(int $id_post): static
    {
        $this->id_post = $id_post;

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
