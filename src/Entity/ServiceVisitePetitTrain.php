<?php

namespace App\Entity;

use App\Repository\ServiceVisitePetitTrainRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ServiceVisitePetitTrainRepository::class)]
class ServiceVisitePetitTrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service_visite_petit_train:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['service_visite_petit_train:read', 'service_read'])]
    private ?string $parcours = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service_visite_petit_train:read', 'service_read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['service_visite_petit_train:read', 'service_read'])]
    private array $disponibilite = [];

    #[ORM\Column(length: 50)]
    #[Groups(['service_visite_petit_train:read', 'service_read'])]
    private ?string $duree = null;

    #[ORM\ManyToOne(inversedBy: 'serviceVisitePetitTrains')]
    #[Groups(['service_visite_petit_train:read'])]
    private ?Service $service = null;

    #[ORM\Column]
    #[Groups(['service_visite_petit_train:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['service_visite_petit_train:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcours(): ?string
    {
        return $this->parcours;
    }

    public function setParcours(string $parcours): static
    {
        $this->parcours = $parcours;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDisponibilite(): array
    {
        return $this->disponibilite ?? [];
    }

    public function setDisponibilite(array $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

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
