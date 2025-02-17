<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: "animal_consultations")]
class AnimalConsultation
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private string $animalName;

    #[ODM\Field(type: "int")]
    private int $consultationCount = 0;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAnimalName(): string
    {
        return $this->animalName;
    }

    public function setAnimalName(string $name): self
    {
        $this->animalName = $name;
        return $this;
    }

    public function getConsultationCount(): int
    {
        return $this->consultationCount;
    }

    public function incrementConsultationCount(): self
    {
        $this->consultationCount++;
        return $this;
    }
}
