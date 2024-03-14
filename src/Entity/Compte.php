<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20)]
    private ?string $nom_affichage = null;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    #[ORM\Column(length: 40)]
    private ?string $mdp = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dernier_golden_like = null;

    #[ORM\OneToMany(mappedBy: 'suivi_personne_id', targetEntity: Abonnement::class)]
    private Collection $abonnements;

    #[ORM\ManyToOne]
    private ?Photo $photo_id = null;

    #[ORM\ManyToOne]
    private ?Etablissement $etablissement_id = null;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function setNomAffichage(string $nom_affichage): static
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

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setSuiviPersonneId($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getSuiviPersonneId() === $this) {
                $abonnement->setSuiviPersonneId(null);
            }
        }

        return $this;
    }

    public function getPhotoId(): ?Photo
    {
        return $this->photo_id;
    }

    public function setPhotoId(?Photo $photo_id): static
    {
        $this->photo_id = $photo_id;

        return $this;
    }

    public function getEtablissementId(): ?Etablissement
    {
        return $this->etablissement_id;
    }

    public function setEtablissementId(?Etablissement $etablissement_id): static
    {
        $this->etablissement_id = $etablissement_id;

        return $this;
    }
}
