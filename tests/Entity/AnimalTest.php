<?php

// namespace App\Tests\Entity;

// use App\Entity\Animal;
// use App\Entity\Habitat;
// use App\Entity\Race;
// use App\Entity\RapportVeterinaire;
// use App\Entity\Avis;
// use App\Entity\Image;
// use PHPUnit\Framework\TestCase;

// class AnimalTest extends TestCase
// {
//     public function testCreateAnimal()
//     {
//         $animal = new Animal();

//         // Vérification de l'initialisation des collections
//         $this->assertInstanceOf(
//             \Doctrine\Common\Collections\Collection::class,
//             $animal->getRapportVeterinaires()
//         );

//         $this->assertInstanceOf(
//             \Doctrine\Common\Collections\Collection::class,
//             $animal->getAvis()
//         );

//         $this->assertInstanceOf(
//             \Doctrine\Common\Collections\Collection::class,
//             $animal->getImages()
//         );

//         // Vérification de la date de création automatique
//         $this->assertInstanceOf(
//             \DateTimeImmutable::class,
//             $animal->getCreatedAt()
//         );
//     }

//     public function testSetAndGetFirstname()
//     {
//         $animal = new Animal();
//         $animal->setFirstname("Tigre");

//         $this->assertEquals(
//             "Tigre",
//             $animal->getFirstname()
//         );
//     }

//     public function testSetAndGetEtat()
//     {
//         $animal = new Animal();
//         $animal->setEtat("malade");

//         $this->assertEquals(
//             "malade",
//             $animal->getEtat()
//         );
//     }

//     public function testSetAndGetHabitat()
//     {
//         $animal = new Animal();
//         $habitat = new Habitat();
//         $animal->setHabitat($habitat);

//         $this->assertSame(
//             $habitat,
//             $animal->getHabitat()
//         );
//     }

//     public function testSetAndGetRace()
//     {
//         $animal = new Animal();
//         $race = new Race();
//         $animal->setRace($race);

//         $this->assertSame(
//             $race,
//             $animal->getRace()
//         );
//     }

//     public function testSetAndGetUpdatedAt()
//     {
//         $animal = new Animal();
//         $date = new \DateTimeImmutable();
//         $animal->setUpdatedAt($date);

//         $this->assertSame(
//             $date,
//             $animal->getUpdatedAt()
//         );
//     }

//     public function testAddAndRemoveRapportVeterinaire()
//     {
//         $animal = new Animal();
//         $rapport = new RapportVeterinaire();
//         $animal->addRapportVeterinaire($rapport);

//         $this->assertCount(
//             1,
//             $animal->getRapportVeterinaires()
//         );
//         $this->assertSame(
//             $rapport,
//             $animal->getRapportVeterinaires()->first()
//         );

//         $animal->removeRapportVeterinaire($rapport);
//         $this->assertCount(
//             0,
//             $animal->getRapportVeterinaires()
//         );
//     }

//     public function testAddAndRemoveAvis()
//     {
//         $animal = new Animal();
//         $avis = new Avis();
//         $animal->addAvis($avis);

//         $this->assertCount(
//             1,
//             $animal->getAvis()
//         );
//         $this->assertSame(
//             $avis,
//             $animal->getAvis()->first()
//         );

//         $animal->removeAvis($avis);
//         $this->assertCount(
//             0,
//             $animal->getAvis()
//         );
//     }

//     public function testAddAndRemoveImage()
//     {
//         $animal = new Animal();
//         $image = new Image();
//         $animal->addImage($image);

//         $this->assertCount(
//             1,
//             $animal->getImages()
//         );
//         $this->assertSame(
//             $image,
//             $animal->getImages()->first()
//         );

//         $animal->removeImage($image);
//         $this->assertCount(
//             0,
//             $animal->getImages()
//         );
//     }
// }
