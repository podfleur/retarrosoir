<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB)]
    private $donnees_photo = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Format $format_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDonneesPhoto()
    {
        return $this->donnees_photo;
    }

    public function setDonneesPhoto($donnees_photo): static
    {
        $this->donnees_photo = $donnees_photo;

        return $this;
    }

    public function getFormatId(): ?Format
    {
        return $this->format_id;
    }

    public function setFormatId(?Format $format_id): static
    {
        $this->format_id = $format_id;

        return $this;
    }
}
