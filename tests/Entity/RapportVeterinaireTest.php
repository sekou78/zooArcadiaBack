<?php

namespace App\Tests\Entity;

use App\Entity\RapportVeterinaire;
use App\Entity\Animal;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RapportVeterinaireTest extends TestCase
{
    public function testCreateRapportVeterinaire()
    {
        $rapport = new RapportVeterinaire();

        // Vérification des valeurs initiales
        $this->assertNull($rapport->getId());
        $this->assertNull($rapport->getDate());
        $this->assertNull($rapport->getEtat());
        $this->assertNull($rapport->getAnimal());
        $this->assertNull($rapport->getVeterinaire());
        $this->assertNull($rapport->getNourritureProposee());
        $this->assertNull($rapport->getQuantiteNourriture());
        $this->assertNull($rapport->getCommentaireHabitat());
        $this->assertNull($rapport->getCreatedAt());
        $this->assertNull($rapport->getUpdatedAt());
    }

    public function testSetAndGetDate()
    {
        $rapport = new RapportVeterinaire();
        $date = new \DateTime();
        $rapport->setDate($date);

        $this->assertSame(
            $date,
            $rapport->getDate()
        );
    }

    public function testSetAndGetEtat()
    {
        $rapport = new RapportVeterinaire();
        $etat = "En observation";
        $rapport->setEtat($etat);

        $this->assertEquals(
            $etat,
            $rapport->getEtat()
        );
    }

    public function testSetAndGetAnimal()
    {
        $rapport = new RapportVeterinaire();
        $animal = new Animal();
        $rapport->setAnimal($animal);

        $this->assertSame(
            $animal,
            $rapport->getAnimal()
        );
    }

    public function testSetAndGetVeterinaire()
    {
        $rapport = new RapportVeterinaire();
        $veterinaire = new User();
        $rapport->setVeterinaire($veterinaire);

        $this->assertSame(
            $veterinaire,
            $rapport->getVeterinaire()
        );
    }

    public function testSetAndGetNourritureProposee()
    {
        $rapport = new RapportVeterinaire();
        $nourriture = "Croquettes spéciales";
        $rapport->setNourritureProposee($nourriture);

        $this->assertEquals(
            $nourriture,
            $rapport->getNourritureProposee()
        );
    }

    public function testSetAndGetQuantiteNourriture()
    {
        $rapport = new RapportVeterinaire();
        $quantite = 2.5;
        $rapport->setQuantiteNourriture($quantite);

        $this->assertEquals(
            $quantite,
            $rapport->getQuantiteNourriture()
        );
    }

    public function testSetAndGetCommentaireHabitat()
    {
        $rapport = new RapportVeterinaire();
        $commentaire = "Habitat propre mais humide.";
        $rapport->setCommentaireHabitat($commentaire);

        $this->assertEquals(
            $commentaire,
            $rapport->getCommentaireHabitat()
        );
    }

    public function testSetAndGetCreatedAt()
    {
        $rapport = new RapportVeterinaire();
        $date = new \DateTimeImmutable();
        $rapport->setCreatedAt($date);

        $this->assertSame(
            $date,
            $rapport->getCreatedAt()
        );
    }

    public function testSetAndGetUpdatedAt()
    {
        $rapport = new RapportVeterinaire();
        $date = new \DateTimeImmutable();
        $rapport->setUpdatedAt($date);

        $this->assertSame(
            $date,
            $rapport->getUpdatedAt()
        );
    }
}
