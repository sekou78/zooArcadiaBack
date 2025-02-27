<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[Assert\NotBlank]
    #[Assert\All([
        new Assert\Choice(
            choices: [
                'ROLE_ADMIN',
                'ROLE_EMPLOYE',
                'ROLE_VETERINAIRE'
            ],
            message: 'Choisissez un rôle valide.'
        )
    ])]
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column(length: 50)]
    #[Groups(['user_read', 'user_write', 'service_user_read'])]  // Exemple de groupes
    private ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken;

    /**@throws \Exception */
    public function __construct()
    {
        $this->apiToken = bin2hex(random_bytes(50));
        $this->services = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        if (empty($this->roles)) {
            $this->roles = ['ROLE_USER'];
        }
        $this->serviceAnimaux = new ArrayCollection();
    }

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'veterinaire', targetEntity: RapportVeterinaire::class)]
    #[Groups(['user_read'])]
    private Collection $rapportsVeterinaires;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'utilisateurs')]
    private Collection $services;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, ServiceAnimaux>
     */
    #[ORM\ManyToMany(targetEntity: ServiceAnimaux::class, mappedBy: 'users')]
    private Collection $serviceAnimaux;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function setAsAdmin(): void
    {
        $this->roles = ['ROLE_ADMIN'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

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

    public function addRapportVeterinaire(RapportVeterinaire $rapport): static
    {
        if (!$this->rapportsVeterinaires->contains($rapport)) {
            $this->rapportsVeterinaires->add($rapport);
            $rapport->setVeterinaire($this);  // Assure la liaison inverse
        }

        return $this;
    }

    public function removeRapportVeterinaire(RapportVeterinaire $rapport): static
    {
        if ($this->rapportsVeterinaires->removeElement($rapport)) {
            if ($rapport->getVeterinaire() === $this) {
                $rapport->setVeterinaire(null);  // Enlève la liaison inverse
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addUtilisateur($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeUtilisateur($this);
        }

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

    public function getServiceAnimaux(): Collection
    {
        return $this->serviceAnimaux;
    }

    public function addServiceAnimaux(ServiceAnimaux $serviceAnimaux): static
    {
        if (!$this->serviceAnimaux->contains($serviceAnimaux)) {
            $this->serviceAnimaux->add($serviceAnimaux);
            $serviceAnimaux->addUser($this);
        }
        return $this;
    }

    public function removeServiceAnimaux(ServiceAnimaux $serviceAnimaux): static
    {
        if ($this->serviceAnimaux->removeElement($serviceAnimaux)) {
            $serviceAnimaux->removeUser($this);
        }
        return $this;
    }
}
