<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service_restaurant:read', 'service_visite_petit_train:read', 'service_user_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(['service_restaurant:read', 'service_visite_petit_train:read', 'service_user_read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 250, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(['service_restaurant:read', 'service_visite_petit_train:read', 'service_user_read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'services')]
    #[Groups('service_user_read')]
    private Collection $utilisateurs;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, ServiceRestaurant>
     */
    #[ORM\OneToMany(targetEntity: ServiceRestaurant::class, mappedBy: 'service')]
    private Collection $serviceRestaurants;

    /**
     * @var Collection<int, ServiceVisitePetitTrain>
     */
    #[ORM\OneToMany(targetEntity: ServiceVisitePetitTrain::class, mappedBy: 'service')]
    private Collection $serviceVisitePetitTrains;

    /**
     * @var Collection<int, Habitat>
     */
    #[ORM\OneToMany(targetEntity: Habitat::class, mappedBy: 'service')]
    private Collection $habitats;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->serviceRestaurants = new ArrayCollection();
        $this->serviceVisitePetitTrains = new ArrayCollection();
        $this->habitats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(User $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): static
    {
        $this->utilisateurs->removeElement($utilisateur);

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ServiceRestaurant>
     */
    public function getServiceRestaurants(): Collection
    {
        return $this->serviceRestaurants;
    }

    public function addServiceRestaurant(ServiceRestaurant $serviceRestaurant): static
    {
        if (!$this->serviceRestaurants->contains($serviceRestaurant)) {
            $this->serviceRestaurants->add($serviceRestaurant);
            $serviceRestaurant->setService($this);
        }

        return $this;
    }

    public function removeServiceRestaurant(ServiceRestaurant $serviceRestaurant): static
    {
        if ($this->serviceRestaurants->removeElement($serviceRestaurant)) {
            // set the owning side to null (unless already changed)
            if ($serviceRestaurant->getService() === $this) {
                $serviceRestaurant->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceVisitePetitTrain>
     */
    public function getServiceVisitePetitTrains(): Collection
    {
        return $this->serviceVisitePetitTrains;
    }

    public function addServiceVisitePetitTrain(ServiceVisitePetitTrain $serviceVisitePetitTrain): static
    {
        if (!$this->serviceVisitePetitTrains->contains($serviceVisitePetitTrain)) {
            $this->serviceVisitePetitTrains->add($serviceVisitePetitTrain);
            $serviceVisitePetitTrain->setService($this);
        }

        return $this;
    }

    public function removeServiceVisitePetitTrain(ServiceVisitePetitTrain $serviceVisitePetitTrain): static
    {
        if ($this->serviceVisitePetitTrains->removeElement($serviceVisitePetitTrain)) {
            // set the owning side to null (unless already changed)
            if ($serviceVisitePetitTrain->getService() === $this) {
                $serviceVisitePetitTrain->setService(null);
            }
        }

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
            $habitat->setService($this);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): static
    {
        if ($this->habitats->removeElement($habitat)) {
            // set the owning side to null (unless already changed)
            if ($habitat->getService() === $this) {
                $habitat->setService(null);
            }
        }

        return $this;
    }
}
