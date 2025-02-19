<?php

namespace App\Tests\Entity;

use App\Entity\Habitat;
use App\Entity\Animal;
use App\Entity\Image;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class HabitatTest extends TestCase
{
    public function testCreateHabitat()
    {
        $habitat = new Habitat();

        // Vérification des valeurs initiales
        $this->assertNull($habitat->getId());
        $this->assertNull($habitat->getName());
        $this->assertNull($habitat->getDescription());
        $this->assertNull($habitat->getCommentHabitat());
        $this->assertNull($habitat->getCreatedAt());
        $this->assertNull($habitat->getUpdatedAt());
        $this->assertNull($habitat->getService());

        // Vérification des collections initialisées
        $this->assertInstanceOf(
            \Doctrine\Common\Collections\Collection::class,
            $habitat->getAnimals()
        );
        $this->assertInstanceOf(
            \Doctrine\Common\Collections\Collection::class,
            $habitat->getImages()
        );
        $this->assertCount(
            0,
            $habitat->getAnimals()
        );
        $this->assertCount(
            0,
            $habitat->getImages()
        );
    }

    public function testSetAndGetName()
    {
        $habitat = new Habitat();
        $habitat->setName("Savane");

        $this->assertEquals(
            "Savane",
            $habitat->getName()
        );
    }

    public function testSetAndGetDescription()
    {
        $habitat = new Habitat();
        $habitat->setDescription(
            "Une vaste étendue herbeuse avec quelques arbres."
        );

        $this->assertEquals(
            "Une vaste étendue herbeuse avec quelques arbres.",
            $habitat->getDescription()
        );
    }

    public function testSetAndGetCommentHabitat()
    {
        $habitat = new Habitat();
        $habitat->setCommentHabitat(
            "Besoin d'entretien régulier."
        );

        $this->assertEquals(
            "Besoin d'entretien régulier.",
            $habitat->getCommentHabitat()
        );
    }

    public function testSetAndGetCreatedAt()
    {
        $habitat = new Habitat();
        $date = new \DateTimeImmutable();
        $habitat->setCreatedAt($date);

        $this->assertSame(
            $date,
            $habitat->getCreatedAt()
        );
    }

    public function testSetAndGetUpdatedAt()
    {
        $habitat = new Habitat();
        $date = new \DateTimeImmutable();
        $habitat->setUpdatedAt($date);

        $this->assertSame(
            $date,
            $habitat->getUpdatedAt()
        );
    }

    public function testSetAndGetService()
    {
        $habitat = new Habitat();
        $service = new Service();
        $habitat->setService($service);

        $this->assertSame(
            $service,
            $habitat->getService()
        );
    }

    public function testAddAndRemoveAnimal()
    {
        $habitat = new Habitat();
        $animal = new Animal();

        $habitat->addAnimal($animal);

        $this->assertCount(
            1,
            $habitat->getAnimals()
        );
        $this->assertSame(
            $animal,
            $habitat->getAnimals()->first()
        );
        $this->assertSame(
            $habitat,
            $animal->getHabitat()
        );

        $habitat->removeAnimal($animal);

        $this->assertCount(
            0,
            $habitat->getAnimals()
        );
        $this->assertNull(
            $animal->getHabitat()
        );
    }

    public function testAddAndRemoveImage()
    {
        $habitat = new Habitat();
        $image = new Image();

        $habitat->addImage($image);

        $this->assertCount(
            1,
            $habitat->getImages()
        );
        $this->assertSame(
            $image,
            $habitat->getImages()->first()
        );
        $this->assertSame(
            $habitat,
            $image->getHabitat()
        );

        $habitat->removeImage($image);

        $this->assertCount(
            0,
            $habitat->getImages()
        );
        $this->assertNull(
            $image->getHabitat()
        );
    }
}
