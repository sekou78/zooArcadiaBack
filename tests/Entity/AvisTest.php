<?php

// namespace App\Tests\Entity;

// use App\Entity\Avis;
// use App\Entity\Animal;
// use PHPUnit\Framework\TestCase;

// class AvisTest extends TestCase
// {
//     public function testCreateAvis()
//     {
//         $avis = new Avis();

//         // Vérification que les valeurs par défaut sont nulles
//         $this->assertNull($avis->getId());
//         $this->assertNull($avis->getPseudo());
//         $this->assertNull($avis->getComments());
//         $this->assertNull($avis->isVisible());
//         $this->assertNull($avis->getAnimal());
//         $this->assertNull($avis->getCreatedAt());
//     }

//     public function testSetAndGetPseudo()
//     {
//         $avis = new Avis();
//         $avis->setPseudo("JohnDoe");

//         $this->assertEquals(
//             "JohnDoe",
//             $avis->getPseudo()
//         );
//     }

//     public function testSetAndGetComments()
//     {
//         $avis = new Avis();
//         $avis->setComments("C'est un très bon animal.");

//         $this->assertEquals(
//             "C'est un très bon animal.",
//             $avis->getComments()
//         );
//     }

//     public function testSetAndGetIsVisible()
//     {
//         $avis = new Avis();
//         $avis->setVisible(true);

//         $this->assertTrue($avis->isVisible());

//         $avis->setVisible(false);
//         $this->assertFalse($avis->isVisible());
//     }

//     public function testSetAndGetAnimal()
//     {
//         $avis = new Avis();
//         $animal = new Animal();
//         $avis->setAnimal($animal);

//         $this->assertSame(
//             $animal,
//             $avis->getAnimal()
//         );
//     }

//     public function testSetAndGetCreatedAt()
//     {
//         $avis = new Avis();
//         $date = new \DateTimeImmutable();
//         $avis->setCreatedAt($date);

//         $this->assertSame(
//             $date,
//             $avis->getCreatedAt()
//         );
//     }
// }
