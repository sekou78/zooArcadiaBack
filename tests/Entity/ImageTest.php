<?php

// namespace App\Tests\Entity;

// use App\Entity\Image;
// use App\Entity\Animal;
// use App\Entity\Habitat;
// use PHPUnit\Framework\TestCase;

// class ImageTest extends TestCase
// {
//     public function testCreateImage()
//     {
//         $image = new Image();

//         // Vérification des valeurs initiales
//         $this->assertNull($image->getId());
//         $this->assertNull($image->getImageData());
//         $this->assertNull($image->getFilePath());
//         $this->assertNull($image->getHabitat());
//         $this->assertNull($image->getAnimal());
//         $this->assertNull($image->getUpdatedAt());

//         // Vérification de la date de création automatique
//         $this->assertInstanceOf(
//             \DateTimeImmutable::class,
//             $image->getCreatedAt()
//         );
//     }

//     public function testSetAndGetImageData()
//     {
//         $image = new Image();
//         $data = file_get_contents(__FILE__); // Simulation de données binaires
//         $image->setImageData($data);

//         $this->assertSame(
//             $data,
//             $image->getImageData()
//         );
//     }

//     public function testSetAndGetFilePath()
//     {
//         $image = new Image();
//         $filePath = "https://example.com/image.jpg";
//         $image->setFilePath($filePath);

//         $this->assertEquals(
//             $filePath,
//             $image->getFilePath()
//         );
//     }

//     public function testSetAndGetHabitat()
//     {
//         $image = new Image();
//         $habitat = new Habitat();
//         $image->setHabitat($habitat);

//         $this->assertSame(
//             $habitat,
//             $image->getHabitat()
//         );
//     }

//     public function testSetAndGetAnimal()
//     {
//         $image = new Image();
//         $animal = new Animal();
//         $image->setAnimal($animal);

//         $this->assertSame(
//             $animal,
//             $image->getAnimal()
//         );
//     }

//     public function testSetUpdatedAt()
//     {
//         $image = new Image();
//         $this->assertNull($image->getUpdatedAt());

//         $image->setUpdatedAt();
//         $this->assertInstanceOf(
//             \DateTimeImmutable::class,
//             $image->getUpdatedAt()
//         );
//     }
// }
