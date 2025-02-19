<?php

namespace App\Tests\Entity;

use App\Entity\Race;
use App\Entity\Animal;
use PHPUnit\Framework\TestCase;

class RaceTest extends TestCase
{
    public function testCreateRace()
    {
        $race = new Race();

        // Vérification des valeurs initiales
        $this->assertNull($race->getId());
        $this->assertNull($race->getLabel());
        $this->assertInstanceOf(
            \Doctrine\Common\Collections\Collection::class,
            $race->getAnimals()
        );
        $this->assertCount(0, $race->getAnimals());
        $this->assertNull($race->getCreatedAt());
        $this->assertNull($race->getUpdatedAt());
    }

    public function testSetAndGetLabel()
    {
        $race = new Race();
        $label = "Lion";
        $race->setLabel($label);

        $this->assertEquals(
            $label,
            $race->getLabel()
        );
    }

    public function testAddAndRemoveAnimal()
    {
        $race = new Race();
        $animal = new Animal();

        // Ajout d'un animal
        $race->addAnimal($animal);
        $this->assertCount(
            1,
            $race->getAnimals()
        );
        $this->assertSame(
            $animal,
            $race->getAnimals()->first()
        );

        // Suppression de l'animal
        $race->removeAnimal($animal);
        $this->assertCount(
            0,
            $race->getAnimals()
        );
    }

    public function testSetAndGetCreatedAt()
    {
        $race = new Race();
        $date = new \DateTimeImmutable();
        $race->setCreatedAt($date);

        $this->assertSame(
            $date,
            $race->getCreatedAt()
        );
    }

    public function testSetAndGetUpdatedAt()
    {
        $race = new Race();
        $date = new \DateTimeImmutable();
        $race->setUpdatedAt($date);

        $this->assertSame(
            $date,
            $race->getUpdatedAt()
        );
    }
}
