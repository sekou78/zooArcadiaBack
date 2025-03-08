<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $imageData;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Habitat $habitat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url]
    private ?string $filePath = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Service $service = null;

    /**
     * @var Collection<int, Habitat>
     */
    #[ORM\ManyToMany(targetEntity: Habitat::class, mappedBy: 'image')]
    private Collection $habitats;

    /**
     * @var Collection<int, ServiceRestaurant>
     */
    #[ORM\ManyToMany(targetEntity: ServiceRestaurant::class, inversedBy: 'images')]
    private Collection $serviceRestaurant;

    /**
     * @var Collection<int, ServiceVisitePetitTrain>
     */
    #[ORM\ManyToMany(targetEntity: ServiceVisitePetitTrain::class, inversedBy: 'images')]
    private Collection $serviceVisitePetitTrain;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->habitats = new ArrayCollection();
        $this->serviceRestaurant = new ArrayCollection();
        $this->serviceVisitePetitTrain = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageData()
    {
        return $this->imageData;
    }

    public function setImageData($imageData): static
    {
        $this->imageData = $imageData;

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

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

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection<int, Habitat>
     */
    public function getHabitats(): Collection
    {
        return $this->habitats;
    }

    public function addHabitat(Habitat $habitat): static
    {
        if (!$this->habitats->contains($habitat)) {
            $this->habitats->add($habitat);
            $habitat->addImage($this);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): static
    {
        if ($this->habitats->removeElement($habitat)) {
            $habitat->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceRestaurant>
     */
    public function getServiceRestaurant(): Collection
    {
        return $this->serviceRestaurant;
    }

    public function addServiceRestaurant(ServiceRestaurant $serviceRestaurant): static
    {
        if (!$this->serviceRestaurant->contains($serviceRestaurant)) {
            $this->serviceRestaurant->add($serviceRestaurant);
        }

        return $this;
    }

    public function removeServiceRestaurant(ServiceRestaurant $serviceRestaurant): static
    {
        $this->serviceRestaurant->removeElement($serviceRestaurant);

        return $this;
    }

    /**
     * @return Collection<int, ServiceVisitePetitTrain>
     */
    public function getServiceVisitePetitTrain(): Collection
    {
        return $this->serviceVisitePetitTrain;
    }

    public function addServiceVisitePetitTrain(ServiceVisitePetitTrain $serviceVisitePetitTrain): static
    {
        if (!$this->serviceVisitePetitTrain->contains($serviceVisitePetitTrain)) {
            $this->serviceVisitePetitTrain->add($serviceVisitePetitTrain);
        }

        return $this;
    }

    public function removeServiceVisitePetitTrain(ServiceVisitePetitTrain $serviceVisitePetitTrain): static
    {
        $this->serviceVisitePetitTrain->removeElement($serviceVisitePetitTrain);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
