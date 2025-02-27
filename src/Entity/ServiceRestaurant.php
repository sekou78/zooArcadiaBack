<?php

namespace App\Entity;

use App\Repository\ServiceRestaurantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRestaurantRepository::class)]
class ServiceRestaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service_restaurant:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['service_restaurant:read', 'service_read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service_restaurant:read', 'service_read'])]
    private ?string $description = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['service_restaurant:read', 'service_read'])]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['service_restaurant:read', 'service_read'])]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column]
    #[Groups(['service_visite_petit_train:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['service_visite_petit_train:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'serviceRestaurants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['service_restaurant:read'])]
    private ?Service $service = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): static
    {
        $this->heureDebut = $heureDebut;
        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): static
    {
        $this->heureFin = $heureFin;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }
}
