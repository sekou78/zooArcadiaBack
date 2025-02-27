<?php

namespace App\Entity;

use App\Repository\RapportVeterinaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RapportVeterinaireRepository::class)]
class RapportVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rapport:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['rapport:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['rapport:read'])]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'rapportVeterinaires')]
    #[Groups(['rapport:read', 'rapportVeterinaire:write'])] // Vérifie bien ces groupes
    private ?Animal $animal = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rapportsVeterinaires')]
    #[Groups(["rapportVeterinaire:write"])] // Groupe pour éviter la sérialisation de User côté lecture
    private ?User $veterinaire = null;

    #[ORM\Column(length: 255)]
    #[Groups(['rapport:read'])]
    private ?string $nourritureProposee = null;

    #[ORM\Column(type: "float")]
    #[Groups(['rapport:read'])]
    private ?float $quantiteNourriture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['rapport:read'])]
    private ?string $commentaireHabitat = null;

    #[ORM\Column]
    #[Groups(['rapport:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getVeterinaire(): ?User
    {
        return $this->veterinaire;
    }

    public function setVeterinaire(?User $veterinaire): static
    {
        $this->veterinaire = $veterinaire;

        return $this;
    }

    public function getNourritureProposee(): ?string
    {
        return $this->nourritureProposee;
    }

    public function setNourritureProposee(string $nourritureProposee): static
    {
        $this->nourritureProposee = $nourritureProposee;

        return $this;
    }

    public function getQuantiteNourriture(): ?float
    {
        return $this->quantiteNourriture;
    }

    public function setQuantiteNourriture(float $quantiteNourriture): static
    {
        $this->quantiteNourriture = $quantiteNourriture;

        return $this;
    }

    public function getCommentaireHabitat(): ?string
    {
        return $this->commentaireHabitat;
    }

    public function setCommentaireHabitat(?string $commentaireHabitat): static
    {
        $this->commentaireHabitat = $commentaireHabitat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
