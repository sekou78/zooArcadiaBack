<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB)]
    private $imageData;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Habitat $habitat = null;

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
}
